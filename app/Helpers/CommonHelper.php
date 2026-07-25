<?php

namespace App\Helpers;

class CommonHelper
{
    public static function displayPrintButtonInBlade($divId = '', $style = '', $mode = '1')
    {
        ob_start(); ?>
        <button class="btn btn-sm btn-primary"
            onclick="printView('<?php echo $divId; ?>', '<?php echo $style; ?>', '<?php echo $mode; ?>')"
            style="<?php echo $style; ?>">
            <i class="lni lni-printer"></i> Print
        </button>
       <?php return ob_get_clean();
    }
}
