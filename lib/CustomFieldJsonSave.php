<?php
namespace Mderrdx\Custom\Field;

class CustomFieldJsonSave extends \Bitrix\Main\UserField\Types\BaseType
{

	public static function getDescription(): array
	{
		return [
			'USER_TYPE_ID' => 'custom_type_field_string',
			"CLASS_NAME" => __CLASS__,
			'DESCRIPTION' => "Пример создания поля",
			'BASE_TYPE' => \CUserTypeManager::BASE_TYPE_STRING,
		];
	}

	public static function onAfterFetch(array $userField, array $fetched)
	{
		return json_decode($fetched['VALUE'], true);
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

	public static function onBeforeSave(?array $userField, $value)
	{
		if ($value['tag'] === '' && $value['link'] === '') {
			return null;
		}
		return json_encode($value);
	}
}