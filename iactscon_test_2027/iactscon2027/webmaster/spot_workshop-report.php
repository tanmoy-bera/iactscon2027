<?php include_once("includes/source.php"); ?>

<body>
    <?php include_once("includes/left-menu.php"); ?>
    <header>
        <h2>Workshop Report</h2>
        <?php include_once("includes/header_right.php"); ?>
    </header>

    <div class="body_wrap">
        <div class="page_top_wrap mb-3">
            <div class="page_top_wrap_left">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Workshop Report</li>
                    </ol>
                </nav>
                <h2>Spot Workshop Report</h2>
                <h6>Detailed statistical breakdown and exportable data.</h6>
            </div>
        </div>
        <div class="table_wrap">
            <h><?php circle(); ?>Summary of all workshop registration on the following days</h>
            <table>
                <thead>
                    <tr>
                        <th>Registration Report</th>
                        <th class="text-center">Cash</th>
                        <th class="text-center">Card</th>
                        <th class="text-center">Cheque</th>
                        <th class="text-center">RTGS</th>
                        <th class="text-center">NEFT</th>
                        <th class="text-center">DD</th>
                        <th class="text-center">UPI</th>
                        <th class="text-center">Complimentary</th>
                        <th class="text-center">Zero Value</th>
                        <th class="text-center">Total</th>
                        <th class="action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>01-December-2025</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center badge_success border-0">0</td>
                        <td class="action">
                            <div class="action_div">
                                <a href="#" class="icon_hover badge_primary action-transparent w-auto br-5" title="Mark Delivery"><?php view(); ?></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>02-December-2025</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center">0</td>
                        <td class="text-center badge_success border-0">0</td>
                        <td class="action">
                            <div class="action_div">
                                <a href="#" class="icon_hover badge_primary action-transparent w-auto br-5" title="Mark Delivery"><?php view(); ?></a>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th class="text-right text-muted border-0" colspan="10">Total Workshop</th>
                        <th class="text-center border-0">
                            <h6 class="text-center m-0 text_success">0</h6>
                        </th>
                        <th class="action border-0"></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <?php include_once("includes/popup.php"); ?>
</body>
<?php include_once("includes/js-source.php"); ?>

</html>