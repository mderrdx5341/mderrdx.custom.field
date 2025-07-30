<?php

use \Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) {
    return;
}

if ($e = $APPLICATION->GetException()) {
    echo CAdminMessage::ShowMessage([
        'TYPE' => 'ERROR',
        'MESSAGE' => Loc::getMessage("MOD_INST_ERR"),
        'DETAILS' => $e->GetString(),
        'HTML' => true,
    ]);
} else {
    echo CAdminMessage::ShowNote(Loc::getMessage('MOD_INST_OK'));
}
?>
<form action="<?= $APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
    <input type="submit" name="" value="<?= Loc::getMessage("MOD_BACK"); ?>">
</form>
<?die();?>