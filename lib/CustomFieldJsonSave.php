<?php
namespace Mderrdx\Custom\Field;

class CustomFieldJsonSave extends \Bitrix\Main\UserField\Types\BaseType
{
	public static function getTypeDescription() {
        return [
            'PROPERTY_TYPE' => \CUserTypeManager::BASE_TYPE_STRING,
            'USER_TYPE' => 'custom_type_element_field_string',
            'DESCRIPTION' => 'Пример создания поля',
            'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml'],
			'ConvertToDB' => [__CLASS__, 'ConvertToDB'],
			'ConvertFromDB' => [__CLASS__, 'ConvertFromDB'],
            //'GetSettingsHTML' => [__CLASS__, 'GetSettingsHTML'],
        ];
    }
	

	public static function getDescription(): array
	{
		return [
			'USER_TYPE_ID' => 'custom_type_field_string',
			"CLASS_NAME" => __CLASS__,
			'DESCRIPTION' => "Пример создания поля",
			'BASE_TYPE' => \CUserTypeManager::BASE_TYPE_STRING,
		];
	}

	static function GetDBColumnType(): string
    {
        global $DB;
        switch (strtolower($DB->type)) {
            case "mysql":
                return "text";
            case "oracle":
                return "varchar2(2000 char)";
            case "mssql":
                return "varchar(2000)";
        }
    }

	public static function GetEditFormHTML(array $userField, ?array $additionalParameters) : string
    {
		$r = '';
		$i = 0;
		foreach ($additionalParameters['VALUE'] as $v) {
			$r .= '<div><label>Тег<input name="' 
				. $additionalParameters['NAME'] 
				. "[$i][tag]" .'" value="' . $v['tag'] . '">
				</label><label>Ссылка<input name="'
				. $additionalParameters['NAME'] 
				. "[$i][link]" . '" value="' . $v['link'] . '"></labe></div>';
			$i++;
		}

				$r .= '<div><label>Тег<input name="' 
				. $additionalParameters['NAME'] 
				. "[$i][tag]" .'" value="">
				</label><label>Ссылка<input name="'
				. $additionalParameters['NAME'] 
				. "[$i][link]" . '" value=""></labe></div>';

		return $r;

    }
	
	public static function onAfterFetch(array $userField, array $fetched)
	{
		return json_decode($fetched['VALUE'], true);
	}


	public static function onBeforeSave(?array $userField, $value)
	{
		if ($value['tag'] === '' && $value['link'] === '') {
			return null;
		}
		return json_encode($value);
	}
	
	
	/////iblock
	
	public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		//self::getProducts();
		$html = '';
		if (is_array($value['VALUE'])) {
		$html = 'Товар:<input name="'. $strHTMLControlName["VALUE"] . '[tag]"'. 'value="'.$value['VALUE']['tag'] . '"></input>'
				.'Количество:<input name="'. $strHTMLControlName["VALUE"] . '[link]"'. 'value="' . $value['VALUE']['link'] . '"></input>';
		} else {
			$html = 'Товар:<input name="'. $strHTMLControlName["VALUE"] . '[tag]" value=""></input>'
				.'Количество:<input name="'. $strHTMLControlName["VALUE"] . '[link]" value=""></input>';
		}
		return $html;
	}
	
	
	public static function ConvertToDB($arProperty, $value)
	{
		if ($value['VALUE']['tag'] === '' && $value['VALUE']['link'] === '') {

			return false;
		}
		return json_encode($value);
	}

	public static function ConvertFromDB($arProperty, $value, $format = '')
	{
		return json_decode($value['VALUE'], true);
	}
}