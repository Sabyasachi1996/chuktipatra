<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
    	@page {  
            margin: 0; 
        }
        
        body { 
            padding: 20px;
            margin: 0;
            font-family:'Helvetica';
        } 

        h1,h2,h3,h4,h5,h6 {
            margin: 0;
            font-family:'Helvetica';
        }

        p {
            font-size: 14px;
            margin-bottom: 20px;
            text-align: justify
        }

        li {
            font-size: 14px;
            margin-bottom: 16px;
            text-align: justify
        }
        
        .page-break {
            page-break-after: always;
        }
        .estamp {
            position: absolute;
            top: 0.25cm;
            left: 0.25cm;
            width: 20.5cm;
            height: 9cm;
        }

        .stamp {
            position: relative;
            width: 100%;
        }

        .stamp_number {
            position: absolute;
            top: 4.5cm;
            left: 2cm;
            font-size: 20px;
            color: #fa5151;
        } 

        .stamp_amount {
            position: absolute;
            top: 2.25cm;
            left: 12.5cm;
            font-size: 48px;
            font-weight: 700;
            color: #018bee;
            width: 6cm;
            text-align: center;
        }

        .stamp_amount_words {
            position: absolute;
            top: 5.5cm;
            left: 12cm;
            font-size: 20px;
            font-weight: 700;
            color: #018bee;
            width: 7cm;
            text-align: center;
        }

        .stamp_date {
            position: absolute;
            top: 5.5cm;
            left: 2.75cm;
            width: 5cm;
            font-size: 16px;
            font-weight: 700;
            color: #999;
        }

        .cover-page-content {
            position: absolute; 
            top: 9.5cm; 
            left: 2cm; 
            width: 17cm;
        }
        .page-content {
            position: absolute; 
            top: 2cm; 
            left: 2cm; 
            width: 17cm;
        }

        .border-bottom {
            border-bottom: 1px solid #333;
        }
         
        .text-center {
            text-align: center;
        }

        .my-20 {
            margin: 10px 0;
        }
        .my-40 {
            margin: 20px 0;
        }

        td {
            vertical-align: top;
        }

        .footer {
            position: absolute; 
            bottom: 0.5cm; 
            left: 0cm; 
            right: 0cm;
            text-align: center;
            font-size: 12px;
        }

        .qr_code{
            height: 90px;
            width: 90px;
            position: absolute;
            bottom: 70px;
            left: 70px;
            border: 1px solid #333; 
            padding: 2px;
        }
        
    </style>
</head>
<body>

    {{-- <img src="data:image/png;base64,{!! base64_encode($qr_link) !!}"  class="qr_code"> --}}
    <?php
        $estampdata     =   json_decode($estamp_data);
        $propertydata   =   json_decode($property_data);
        $contractdata   =   json_decode($contract_detail);
        $lessordata     =   json_decode($lessor);
        $lesseedata     =   json_decode($lessee);
        $w1data     =   json_decode($witness_1);
        $w2data     =   json_decode($witness_2);
    ?>

    <div class="cover-page">
        {{-- {{$estampdata->estamp_url}}estamp/{{$estampdata->estamp_num}} <br> --}}
        {{-- <img src="{{$estampdata->estamp_url}}estamp/{{$estampdata->estamp_num}}" class="estamp"> --}}
        <img src="data:image/png;base64,{{$estampdata->estamp_img}}" class="estamp">
        {{-- <div class="stamp">
            <h1 class="stamp_number">{{$estampdata->estamp_num}}</h1>
            <div class="stamp_amount"> Rs. {{$estampdata->estamp_amt}}</div>
            <div class="stamp_amount_words">{{ getAmountInWords($estampdata->estamp_amt) }}</div>
            <div class="stamp_date">{{ strtoupper(date('F d, Y', strtotime($estampdata->estamp_date))) }}</div>
        </div> --}}
        <div class="cover-page-content">
            <p>
                This agreement made on this <strong>{{ date('F d, Y', strtotime($contractdata->agreement_start)) }}</strong> between <strong>{{ $lessordata->lessor_fullname }}</strong>, residing at <strong>{{ $lessordata->lessor_address }}</strong> hereinafter referred to as the <strong>"LESSOR"</strong> of the One Part AND <strong>{{ $lesseedata->lessee_fullname }}</strong>, residing at <strong>{{ $lesseedata->lessee_address }}</strong> hereinafter referred to as the <strong>"LESSEE"</strong> of the other Part;
            </p>

            <p>
                WHEREAS the Lessor is the lawful owner of, and otherwise well sufficiently entitled to Lease Property <strong>{{ $property_address }}</strong> falling in the category, <strong>@if($propertydata->property_type == 'R') RESIDENTIAL @else NON-RESIDENTIAL @endif </strong> and comprising of <strong>{{ $propertydata->property_room }} including {{ $propertydata->property_bed }} Bedrooms, {{ $propertydata->property_bath }} Bathrooms, {{ $propertydata->property_balcony }} Balconies</strong>, present in the Floor <strong>{{ $propertydata->property_floor }}</strong>, @if($propertydata->property_parking == 'YES') with <strong>Parking</strong>, @endif with an area of <strong>{{ $propertydata->property_area }} Square Feet</strong> hereinafter referred to as the "said premises"; 
            </p>

            <p>
                AND WHEREAS at the request of the Lessee, the Lessor has agreed to let the said premises to the tenant for a term of <strong>{{ $contractdata->agreement_duraion }}</strong> commencing from <strong>{{ date('F d, Y', strtotime($contractdata->agreement_start)) }}</strong> in the manner hereinafter appearing.
            </p>

            <p>
                NOW THIS AGREEMENT WITNESSETH AND IT IS HEREBY AGREED BY AND BETWEEN THE PARTIES AS UNDER: 
            </p>
            
            <ol>
                <li>
                    That the Lessor hereby grant to the Lessee, the right to enter into and use and remain in the said premises and that the Lessee shall be entitled to peacefully possess, and enjoy possession of the said premises, and the other rights herein.
                </li>
                <li>
                    That the lease hereby granted shall, unless cancelled earlier under any provision of this Agreement, remain in force for a period of <strong>{{ $contractdata->agreement_duraion }}</strong>.
                </li>
                <li>
                    That the Lessee will have the option to terminate this lease by giving <strong>{{ $contractdata->notice_period }}</strong> in writing to the Lessor.
                </li>
                <li>
                    That the Lessee shall have no right to create any sub-lease or assign or transfer in any manner the lease or give to any one the possession of the said premises or any part thereof.
                </li>
                <li>
                    That the Lessee shall use the said premises only for residential purposes.
                </li>
                <li>
                    That the Lessor shall, before handing over the said premises, ensure the working of sanitary, electrical and water supply connections and other fittings pertaining to the said premises. It is agreed that it shall be the responsibility of the Lessor for their return in the working condition at the time of re-possession of the said premises (reasonable wear and tear and loss or damage by fire, flood, rains, accident, irresistible force or act of God excepted).
                    
                </li>

            </ol>
        </div>  
    </div>   
        
    <div class="page-break"></div> 

    <div class="page">
        <div class="page-content">
            <ol start="7">
                <li>
                    That the Lessee is not authorized to make any alteration in the construction of the said premises. The Lessee may however install and remove his own fittings and fixtures, provided this is done without causing any excessive damage or loss to the said premises.
                </li>
                <li>
                    That the day to day repair jobs such as fuse blow out, replacement of light bulbs/tubes, leakage of water taps, maintenance of the water pump and other minor repairs, etc., shall be effected by the Lessee at its own cost, and any major repairs, either structural or to the electrical or water connection, plumbing leaks, water seepage shall be attended to by the Lessor. In the event of the Lessor failing to carry out the repairs on receiving notice from the Lessee, the Lessee shall undertake the necessary repairs and the Lessor will be liable to immediately reimburse costs incurred by the Lessee.
                </li>
                <li>
                    That the Lessor or its duly authorized agent shall have the right to enter into or upon the said premises or any part thereof at a mutually arranged convenient time for the purpose of inspection.
                </li>
                <li>
                    That the Lessee shall use the said premises along with its fixtures and fitting in careful and responsible manner and shall handover the premises to the Lessor in working condition (reasonable wear and tear and loss or damage by fire, flood, rains, accidents, irresistible force or act of God excepted).
                </li>
                <li>
                    That in consideration of use of the said premises the Lessee agrees that he shall pay to the Lessor during the period of this agreement, a monthly rent at the rate of <strong>Rs. {{ $contractdata->rent_amount }} ( {{ getAmountInWords($contractdata->rent_amount) }})</strong>. The amount will be paid in advance on or before the date of {{ $contractdata->rent_pay_day }} of every English calendar month.
                </li>    
                <li>
                    It is hereby agreed that if default is made by the lessee in payment of the rent for a period of three months, or in observance and performance of any of the covenants and stipulations hereby contained and on the part to be observed and performed by the lessee, then on such default, the lessor shall be entitled in addition to or in the alternative to any other remedy that may be available to him at this discretion, to terminate the lease and eject the lessee from the said premises; and to take possession thereof as full and absolute owner thereof, provided that a notice in writing shall be given by the lessor to the lessee of his intention to terminate the lease and to take possession of the said premises. If the arrears of rent are paid or the lessee comply with or carry out the covenants and conditions or stipulations, within fifteen days from the service of such notice, then the lessor shall not be entitled to take possession of the said premises.
                </li>
                <li>
                    That in addition to the compensation mentioned above, the Lessee shall pay the actual electricity, maintenance, water bills for the period of the agreement directly to the authorities concerned.
                </li>  
                <li>
                    That the Lessee has paid to the Lessor a sum of <strong>Rs. {{ $contractdata->security_amount }} ({{ getAmountInWords($contractdata->security_amount) }})</strong> as deposit, free of interest, which the Lessor does accept and acknowledge. This deposit is for the due performance and observance of the terms and conditions of this Agreement. The deposit shall be returned to the Lessee simultaneously with the Lessee vacating the said premises. In the event of failure on the part of the Lessor to refund the said deposit amount to the Lessee as aforesaid, the Lessee shall be entitled to continue to use and occupy the said premises without payment of any rent until the Lessor refunds the said amount (without prejudice to the Lessee's rights and remedies in law to recover the deposit).
                </li>    
                <li>
                    That the Lessor shall be responsible for the payment of all taxes and levies pertaining to the said premises including but not limited to House Tax, Property Tax, other cesses, if any, and any other statutory taxes, levied by the Government or Governmental Departments. During the term of this Agreement, the Lessor shall comply with all rules, regulations and requirements of any statutory authority, local, state and central government and governmental departments in relation to the said premises.
                </li>    
            </ol>
        </div>
    </div>

    <div class="page-break"></div> 

    <div class="page">
        <div class="page-content">
            <p>
                IN WITNESS WHEREOF, the parties hereto have set their hands on the day and year first hereinabove mentioned. 
            </p>
            
            <table border="0" cellpadding="3" cellspacing="0">
                <tr>
                    <td width="45%">Lessee,</td>
                    <td width="10%"></td>
                    <td width="45%">Lessor,</td>
                </tr>

                <tr>
                    <td colspan="3"><div  class="my-20"></div></td>
                </tr>

                <tr>
                    <td><strong>{{ $lesseedata->lessee_fullname }}</strong><br>(AADHAAR No: {{ $lesseedata->lessee_aadhaar }})</td>
                    <td width="10%"></td>
                    <td><strong>{{ $lessordata->lessor_fullname }}</strong><br>(AADHAAR No: {{ $lessordata->lessor_aadhaar }})</td>
                </tr>
                <tr>
                    <td>{{ $lesseedata->lessee_address }}</td>
                    <td width="10%"></td>
                    <td>{{ $lessordata->lessor_address }}</td>
                </tr>

                <tr>
                    <td colspan="3"><div  class="my-40"></div></td>
                </tr>

                <tr>
                    <td width="45%" class="border-bottom">WITNESS ONE:</td>
                    <td width="10%"></td>
                    <td width="45%" class="border-bottom">WITNESS TWO:</td>
                </tr>
                <tr>
                    <td><strong>{{ $w1data->witness_1_fullname }}</strong><br>(AADHAAR No: {{ $w1data->witness_1_aadhaar }})</td>
                    <td width="10%"></td>
                    <td><strong>{{ $w2data->witness_2_fullname }}</strong><br>(AADHAAR No: {{ $w2data->witness_2_aadhaar }})</td>
                </tr>
                <tr>
                    <td>{{ $w1data->witness_1_address }}</td>
                    <td width="10%"></td>
                    <td>{{ $w2data->witness_2_address }}</td>
                </tr>

            </table>
        </div>   
    </div>    
    
</body>
</html> 