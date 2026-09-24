<?php
include_once('includes/frontend.init.php');
include_once("includes/function.frontend.registration.php");
include_once("includes/function.edit.php");
include_once("includes/function.registration.php");
include_once('includes/function.delegate.php');
include_once('includes/function.invoice.php');
include_once('includes/function.accompany.php');
include_once('includes/function.workshop.php');
include_once('includes/function.registration.php');
include_once('includes/function.messaging.php');
include_once('includes/function.accommodation.php');
include_once("includes/function.dinner.php");
include_once('webmaster/engine/class.common.extended.php');

$act = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch ($act) {
    case 'check_availability':
        check_availability();
        break;
}

/**
 * Check availability for hotel/room bookings
 * Handles AJAX requests for seat limit validation
 */
function check_availability() {
    global $mycms, $currentCutoffId;
    
    // Set JSON header
    header('Content-Type: application/json');
    
    // Debug: Log the request
    error_log("=== check_availability called ===");
    error_log("POST data: " . print_r($_POST, true));
    
    // Get POST data
    $hotelId = isset($_POST['hotel_id']) ? (int)$_POST['hotel_id'] : 0;
    $roomTypeId = isset($_POST['room_type_id']) ? (int)$_POST['room_type_id'] : 0;
    $checkInDate = isset($_POST['check_in_date']) ? $_POST['check_in_date'] : '';
    $checkOutDate = isset($_POST['check_out_date']) ? $_POST['check_out_date'] : '';
    
    // FIX: Use floatval instead of (int) to support decimals (0.5, 1.5, etc.)
    $quantity = isset($_POST['quantity']) ? floatval($_POST['quantity']) : 1;
    
    // Validate required fields
    if (!$hotelId || !$checkInDate || !$checkOutDate) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required parameters',
            'unavailable_dates' => [],
            'available_seats' => []
        ]);
        exit;
    }
    
    // Validate date range
    $date1 = strtotime($checkInDate);
    $date2 = strtotime($checkOutDate);
    
    if ($date1 >= $date2) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid date range - Check-out must be after check-in',
            'unavailable_dates' => [],
            'available_seats' => []
        ]);
        exit;
    }
    
    // Get all dates in the range (excluding checkout date)
    $dates = getDatesInRange($checkInDate, $checkOutDate);
    $unavailableDates = [];
    $availableSeats = [];
    
    // Check each date's availability
    foreach ($dates as $date) {
        $available = getAvailableSeatLimitForDate($hotelId, $roomTypeId, $date);
        $availableSeats[$date] = $available;
        
        // FIX: Compare as floats, not integers
        if ($available < $quantity) {
            $unavailableDates[] = [
                'date' => $date,
                'available' => $available,
                'requested' => $quantity,
                'shortfall' => $quantity - $available
            ];
        }
    }
    
    // Return response
    if (!empty($unavailableDates)) {
        echo json_encode([
            'success' => false,
            'message' => 'Not enough rooms available for selected dates',
            'unavailable_dates' => $unavailableDates,
            'available_seats' => $availableSeats
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Rooms available for all selected dates',
            'available_seats' => $availableSeats,
            'min_available' => !empty($availableSeats) ? min($availableSeats) : 0
        ]);
    }
    exit;
}

/**
 * Get all dates between start and end date (excluding end date)
 */
function getDatesInRange($startDate, $endDate) {
    $dates = [];
    $current = strtotime($startDate);
    $end = strtotime($endDate);
    $end = strtotime('-1 day', $end); // Exclude checkout date
    
    while ($current <= $end) {
        $dates[] = date('Y-m-d', $current);
        $current = strtotime('+1 day', $current);
    }
    
    return $dates;
}

/**
 * Get available seat limit for a specific date
 * FIX: Return as float to support decimal availability
 */
function getAvailableSeatLimitForDate($hotelId, $roomTypeId, $targetDate) {
    global $mycms;
    
    // Step 1: Get total limit for this date
    $totalLimit = getTotalSeatLimit($hotelId, $roomTypeId, $targetDate);
    
    if ($totalLimit == 0) {
        return 0; // No limit set or no availability
    }
    
    // Step 2: Get currently booked seats for this date
    // FIX: Use CAST to handle decimal values properly
    $sqlBooked = array();
    $sqlBooked['QUERY'] = "SELECT COALESCE(SUM(CAST(booking_quantity AS DECIMAL(10,2))), 0) as total_booked
                           FROM " . _DB_REQUEST_ACCOMMODATION_ . "
                           WHERE hotel_id = ? 
                           AND roomTypeId = ?
                           AND status IN ('A', 'P')
                           AND checkin_date <= ?
                           AND checkout_date > ?";
    
    $sqlBooked['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotelId, 'TYP' => 'i');
    $sqlBooked['PARAM'][] = array('FILD' => 'roomTypeId', 'DATA' => $roomTypeId, 'TYP' => 'i');
    $sqlBooked['PARAM'][] = array('FILD' => 'checkin_date', 'DATA' => $targetDate, 'TYP' => 's');
    $sqlBooked['PARAM'][] = array('FILD' => 'checkout_date', 'DATA' => $targetDate, 'TYP' => 's');
    
    $result = $mycms->sql_select($sqlBooked);
    // FIX: Return as float, not int
    $bookedSeats = !empty($result) ? (float)$result[0]['total_booked'] : 0;
    
    // Step 3: Calculate available seats (as float)
    $available = $totalLimit - $bookedSeats;
    
    // Return as float with 2 decimal places
    return round(max(0, $available), 2);
}

/**
 * Get total seat limit for a specific date and room type
 * FIX: Return as float to support decimal limits (0.5 rooms)
 */
function getTotalSeatLimit($hotelId, $roomTypeId, $targetDate) {
    global $mycms;
    
    $totalLimit = 0;
    
    if ($roomTypeId == 0) {
        // Hotel-level limit from rcg_accommodation_checkin_date
        $sqlLimit = array();
        $sqlLimit['QUERY'] = "SELECT CAST(seat_limit AS DECIMAL(10,2)) as seat_limit 
                              FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
                              WHERE hotel_id = ? 
                              AND check_in_date = ? 
                              AND status = 'A'
                              LIMIT 1";
        
        $sqlLimit['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotelId, 'TYP' => 'i');
        $sqlLimit['PARAM'][] = array('FILD' => 'check_in_date', 'DATA' => $targetDate, 'TYP' => 's');
        
        $result = $mycms->sql_select($sqlLimit);
        $totalLimit = !empty($result) ? (float)$result[0]['seat_limit'] : 0;
        
    } else {
        // First try: Room-specific limit from rcg_accommodation_seat_limit
        $sqlRoomLimit = array();
        $sqlRoomLimit['QUERY'] = "SELECT CAST(seat_limit AS DECIMAL(10,2)) as seat_limit 
                                  FROM `rcg_accommodation_seat_limit`
                                  WHERE hotel_id = ? 
                                  AND room_id = ? 
                                  AND check_in_date = ? 
                                  AND status = 'A'
                                  LIMIT 1";
        
        $sqlRoomLimit['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotelId, 'TYP' => 'i');
        $sqlRoomLimit['PARAM'][] = array('FILD' => 'room_id', 'DATA' => $roomTypeId, 'TYP' => 'i');
        $sqlRoomLimit['PARAM'][] = array('FILD' => 'check_in_date', 'DATA' => $targetDate, 'TYP' => 's');
        
        $resultRoom = $mycms->sql_select($sqlRoomLimit);
        
        if (!empty($resultRoom) && $resultRoom[0]['seat_limit'] > 0) {
            $totalLimit = (float)$resultRoom[0]['seat_limit'];
        } else {
            // Fallback to hotel-level limit
            $sqlHotelLimit = array();
            $sqlHotelLimit['QUERY'] = "SELECT CAST(seat_limit AS DECIMAL(10,2)) as seat_limit 
                                       FROM " . _DB_ACCOMMODATION_CHECKIN_DATE_ . " 
                                       WHERE hotel_id = ? 
                                       AND check_in_date = ? 
                                       AND status = 'A'
                                       LIMIT 1";
            
            $sqlHotelLimit['PARAM'][] = array('FILD' => 'hotel_id', 'DATA' => $hotelId, 'TYP' => 'i');
            $sqlHotelLimit['PARAM'][] = array('FILD' => 'check_in_date', 'DATA' => $targetDate, 'TYP' => 's');
            
            $resultHotel = $mycms->sql_select($sqlHotelLimit);
            $totalLimit = !empty($resultHotel) ? (float)$resultHotel[0]['seat_limit'] : 0;
        }
    }
    
    return $totalLimit;
}
?>