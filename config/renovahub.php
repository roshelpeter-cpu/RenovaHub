<?php

return [

    /*
    | RenovaHub earns a platform service fee on successful transactions.
    | The percentage is configured here and then stored on each payment and
    | earning row, so a later change does not rewrite historical records.
    */
    'platform_fee_percent' => (float) env('RENOVAHUB_PLATFORM_FEE_PERCENT', 2),

];
