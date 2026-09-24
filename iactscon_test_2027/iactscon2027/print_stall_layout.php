<?php
// require('lib/tcpdf/tcpdf.php');
// require('lib/vendor/autoload.php');
// require 'lib/dompdf/vendor/autoload.php';
include_once("includes/frontend.init.php");
require 'lib/pdfcrowd-6.4.0/pdfcrowd.php';
// print_r($_REQUEST);die;
// $stall_id = $_REQUEST['stall_id'];
$exb_id = $_REQUEST['exb_id'];
$sqlFetchExhibitor['QUERY']       = "SELECT * FROM " . _DB_EXIBITOR_COMPANY_ . " 
                                        WHERE `status` = 'A' AND `id`= '" . $exb_id . "' ";

$resultExhibitor         = $mycms->sql_select($sqlFetchExhibitor);
$rowExhibitor = $resultExhibitor[0];

$sqlExhibitorLayout['QUERY']        =    "SELECT layout.*,layout_request.id AS layoutReqId
										 FROM " . _DB_EXIBITOR_STALL_BOOKING_REQUEST_ . " layout_request
										
										INNER JOIN " . _DB_EXIBITOR_STALL_BOOKING_LAYOUT_ . " layout
										ON layout_request.stall_id = layout.id
										WHERE layout_request.exhibitor_company_code = '" . $rowExhibitor['exhibitor_company_code'] . "' 
										AND layout_request.status = 'A'
										AND layout.status = 'A'	
										ORDER BY layout_request.stall_id ";

$resultExhibitorLayout  = $mycms->sql_select($sqlExhibitorLayout);
// echo "<pre>";print_r($resultExhibitorLayout);die;
if ($resultExhibitorLayout) {
    $stallArr = array();
    foreach ($resultExhibitorLayout as $key => $rowStall) {
        $stallArr[$key] = $rowStall['stall_number'];
    }
    $stall_id_display= implode(",", $stallArr);
    $stall_id_title = implode("", $stallArr);
}


$html2 = '<body><style>
            
            .dynamic-style {
                background: linear-gradient(to bottom right, hsl(123.51deg 64.42% 36.4% / 15%) 50%, hsl(93.6deg 46% 44.44% / 52%) 0%) !important;
                color: #f7f3a4 !important;
            }
        </style>
    <div style="width: 1000px;margin: auto;">
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td colspan="2" style="background: #1e1e1f;border: 1px solid #383838;width: 100%;justify-content: center;border-radius: 10px;color: white;text-align: center;padding: 16px;font-size: 24px;">
                        Lorem Ipsum</td>
                </tr>
                <tr>
                    <td style="width: 800px; border: 1px solid #383838; border-radius: 10px; background: #1e1e1f;">
                        <table style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td colspan="2" style="height: 0px;">
                                        <p style="margin: 0;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsl(0deg 0% 25% / 55%) 50%);/* text-align: center; */font-size: 11px;color: white;/* display: flex; *//* justify-content: space-between; */padding: 5px 22px;/* margin-left: auto; */">
                                            <span>Walking Road</span>
                                            <span style="
    													padding-left: 610px;
												">Walking Road</span>
                                        </p>
                                        <p class="regi-box-wrap" style="margin: 0;/* background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsl(0deg 0% 25% / 55%) 50%); */text-align: center;font-size: 11px;color: white;/* display: flex; *//* justify-content: space-between; *//* position: absolute; *//* left: 50%; *//* transform: translateX(45px); */gap: 19px;top: 0;padding: 0 !important;margin-top: -20px;">
                                            <div style="background: white;color: black !important;width: 44px;display: inline-block;font-size: 8px;line-height: 10px;margin-left: 187px;margin-right: 22px;">Registration
                                                Box</div>
                                            <div style="background: white;color: black !important;width: 44px;display: inline-block;font-size: 8px;line-height: 10px;">Registration
                                                Box</div>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="entry-td" style="width: 511px;">
                                        <p style="margin: 0;">
                                            <div class="etr-ext" style="color: white;font-size: 9px;background: #595959;padding: 4px 6px;display: inline-block;">Entry<br>Exit</div>
                                            <span style="padding-left: 1px;color: white;font-size: 13px;">Entry</span>
                                            <span style="padding-left: 180px;color: white;font-size: 13px;">Entry</span>
                                            <span style="padding-left: 156px;color: white;font-size: 13px;">Entry</span>
                                        </p>
                                    </td>
                                    <td class="stage-td" rowspan="2" style="width: 268px;">
                                        <div class="stage-box">
                                            <p style="writing-mode: vertical-rl;transform: rotate(180deg);margin: 0;border: 2px solid #ffd66e;padding: 21px 46px;font-size: 20px;font-weight: 800;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);color: white;margin-top: 32px;">
                                                Cultural Event Stage</p>
                                            <div class="etr-ext" style="color: white;font-size: 9px;background: #595959;padding: 4px 6px;display: inline-block;transform: translate(240px, -270px);">Entry<br>Exit</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="width: 24px; float: left;">
                                            <div class="stall43" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">43</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall42" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span  style="display: inline-block;width: 100%;">42</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall41" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">41</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall40" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">40</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall39" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">39</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall38" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">38</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall37" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">37</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall36" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">36</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall35" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">35</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall34" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">34</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall33" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">33</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall32" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">32</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall31" style="display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">31</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                        </div>
                                        <div style="width: 460px;float: left;">
                                            <div style="width: 100%;float: left;">
                                                <div class="stall44" style="display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;margin-left: 34px;">
                                                    <span style="display: inline-block;width: 100%;">44</span>
                                                    <span style="display: inline-block;width: 100%;">2x2</span>
                                                </div>
                                                <div class="stall45" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">45</span>
                                                    <span style="display: inline-block;width: 100%;">2x2</span>
                                                </div>
                                                <div class="stall46" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">46</span>
                                                    <span style="display: inline-block;width: 100%;">2x2</span>
                                                </div>
                                                <div class="stall47" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">47</span>
                                                    <span style="display: inline-block;width: 100%;">2x2</span>
                                                </div>
                                                <div class="stall48" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">48</span>
                                                    <span style="display: inline-block;width: 100%;">2x2</span>
                                                </div>
                                                <div class="stall49" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">49</span>
                                                    <span style="display: inline-block;width: 100%;">2x2</span>
                                                </div>
                                                <div class="stall50" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">50</span>
                                                    <span style="display: inline-block;width: 100%;">2x2</span>
                                                </div>
                                                <div class="stall51" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;margin-left: 31px;">
                                                    <span style="display: inline-block;width: 100%;">51</span>
                                                    <span style="display: inline-block;width: 100%;">2x2</span>
                                                </div>
                                                <div class="stall52" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">52</span>
                                                    <span style="display: inline-block;width: 100%;">2x2</span>
                                                </div>
                                                <div class="stall53" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">53</span>
                                                    <span style="display: inline-block;width: 100%;">2x2</span>
                                                </div>
                                                <div class="stall54" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">54</span>
                                                    <span style="display: inline-block;width: 100%;">2x2</span>
                                                </div>
                                                <div class="stall55" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">55</span>
                                                    <span style="display: inline-block;width: 100%;">2x2</span>
                                                </div>
                                            </div>
                                            <div style="width: 136px;float: left;margin-left: 60px;margin-top: 49px;">
                                                <div class="stall105" style="margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">105</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall104" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">104</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall103" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">103</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall102" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">102</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall101" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">101</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall100" style="/* margin-left: 0px; *//* margin-bottom: 4.5px; */display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">100</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall99" style="display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;margin-left: 0px;">
                                                    <span style="display: inline-block;width: 100%;">99</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall98" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">98</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall97" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">97</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall96" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">96</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                            </div>
                                            <div style="width: 136px;float: left;margin-left: 60px;margin-top: 49px;">
                                                <div class="stall60" style="margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">60</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall59" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">59</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall58" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">58</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall57" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">57</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall56" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">56</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall61" style="/* margin-left: 0px; *//* margin-bottom: 4.5px; */display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">61</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall62" style="display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;margin-left: 0px;">
                                                    <span style="display: inline-block;width: 100%;">62</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall63" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">63</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall64" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">64</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall65" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">65</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                            </div>
                                            <div style="width: 136px;float: left;margin-left: 60px;margin-top: 49px;">
                                                <div class="stall95" style="margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">95</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall94" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">94</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall93" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">93</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall92" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">92</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall91" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">91</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall90" style="/* margin-left: 0px; *//* margin-bottom: 4.5px; */display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">90</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall89" style="display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;margin-left: 0px;">
                                                    <span style="display: inline-block;width: 100%;">89</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall88" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">88</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall87" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">87</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall86" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">86</span><span style="display: inline-block;width: 100%;">2x2</span></div>

                                            </div>
                                            <div style="width: 136px;float: left;margin-left: 60px;margin-top: 49px;">
                                                <div class="stall70" style="margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">70</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall69" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">69</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall68" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">68</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall67" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">67</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall66" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">66</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall71" style="/* margin-left: 0px; *//* margin-bottom: 4.5px; */display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">71</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall72" style="display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;margin-left: 0px;">
                                                    <span style="display: inline-block;width: 100%;">72</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall73" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">73</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall74" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">74</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall75" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">75</span><span style="display: inline-block;width: 100%;">2x2</span></div>

                                            </div>
                                            <div style="width: 136px;float: left;margin-left: 60px;margin-top: 49px;">
                                                <div class="stall85" style="margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">85</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall84" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">84</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall83" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">83</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall82" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">82</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall81" style="margin-left: 0px; margin-bottom: 4.5px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">81</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall80" style="/* margin-left: 0px; *//* margin-bottom: 4.5px; */display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">80</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall79" style="display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;margin-left: 0px;">
                                                    <span style="display: inline-block;width: 100%;">79</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall78" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">78</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall77" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">77</span><span style="display: inline-block;width: 100%;">2x2</span></div>
                                                <div class="stall76" style="margin-left: 0px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                    <span style="display: inline-block;width: 100%;">76</span><span style="display: inline-block;width: 100%;">2x2</span></div>

                                            </div>

                                        </div>
                                        <div style="width: 24px; float: left;">
                                            <div class="stall1" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">1</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall2" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">2</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall3" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">3</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall4" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">4</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall5" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">5</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall6" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">6</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall7" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">7</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall8" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">8</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall9" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">9</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall10" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">10</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall11" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">11</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall12" style="margin-bottom: 4.5px; display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">12</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall13" style="display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">13</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                        </div>
                                        <div style="width: 508px;float: left;margin-top: 3px;">
                                            <div class="etr-ext" style="color: white;font-size: 9px;background: #595959;padding: 4px 6px;display: inline-block;">Entry<br>Exit</div>
                                            <div class="stall30" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">30</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall29" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">29</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall28" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">28</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall27" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">27</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall26" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">26</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall25" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">25</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall24" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">24</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall23" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">23</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall22" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">22</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall21" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">21</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall20" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">20</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall19" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">19</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall18" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">18</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall17" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">17</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall16" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">16</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall15" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">15</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                            <div class="stall14" style="margin-left: 0px;display: inline-block;width: 24px;background: linear-gradient(to bottom right, hsl(0, 0%, 24%) 0%, hsla(0, 0%, 25%, 0) 50%);font-size: 10px;line-height: 9px;overflow: visible;opacity: 1;color: white;cursor: pointer;text-align: center;padding: 4px 0;">
                                                <span style="display: inline-block;width: 100%;">14</span>
                                                <span style="display: inline-block;width: 100%;">2x2</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="width: 200px;vertical-align: top;border: 1px solid #383838;border-radius: 10px;background: #1e1e1f;">
                        <h3 style="padding: 15px 10px;margin: 0;font-size: 17px;border-bottom: 1px solid #383838;color: white;">Exibitor</h3>
                        <li style="list-style: none;display: flex;align-items: center;justify-content: space-between;padding: 13px 10px;list-style: none;">
                            <p style="margin: 0;">
                                <span style="font-size: 17px;color: #ffdb70;display: inline-block;width: 100%;margin-bottom: 5px;">Stall ' . $stall_id_display . '</span>
                                <span style="display: inline-block;width: 100%;color: white;font-size: 18px;">' . $resultExhibitor[0]['exhibitor_company_name'] . '</span>
                                <span style="display: inline-block;width: 100%;color: white;font-size: 18px;">' . $resultExhibitor[0]['exhibitor_company_code'] . '</span>
                            </p>
                        </li>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>';

foreach ($resultExhibitorLayout as $key => $rowStall) {
    $html2 = str_replace(
        ('stall' . $rowStall['id']),
        'dynamic-style',
        $html2
    );
}
// echo $html2;die;


// use Dompdf\Dompdf;
// use Dompdf\Options;

// $options = new Options();
// $options->set('isHtml5ParserEnabled', true);
// $options->set('isRemoteEnabled', true);
// $dompdf = new Dompdf($options);

// $dompdf->loadHtml($html2);
// $dompdf->setPaper('A4', 'landscape');

// $dompdf->render();

// // Display in the browser
// $dompdf->stream("filename.pdf", ["Attachment" => false]);


try {
    // Create an API client instance.
    $client = new \Pdfcrowd\HtmlToImageClient("progyaroy", "ba30d392aab4243b4f28045c32471715");

    // Configure the conversion.
    $client->setOutputFormat("png");

    $filePath = _BASE_URL_ . "images/stall_layouts/";
    // Ensure the directory exists.
    //    $directory = dirname($filePath);
    //    if (!is_dir($directory)) {
    //        mkdir($directory, 0755, true);
    //    }

    // Run the conversion and save the result to a file.
    $fileName = 'Layout_' . $stall_id_title. "_" . $_REQUEST['exb_id'] . '.png';
    $client->convertStringToFile($html2, $fileName);
    $mycms->redirect(_BASE_URL_ . $fileName);
    // copy('HelloWorld.png', $filePath);
    // unlink('HelloWorld.png');
} catch (\Pdfcrowd\Error $why) {
    error_log("Pdfcrowd Error: {$why}\n");
    throw $why;
}
