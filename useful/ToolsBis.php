<?php

require_once 'framework/tools.php';

class ToolsBis extends Tools {

    // Vérifie si tous les champs dont les clés sont passées dans le tableau $fields sont
// présents dans le tableau $arr. Si pas de tableau passé en paramètre, on vérifie dans $_POST.
    public static function check_fields($fields, $arr = null) {
        if ($arr === null)
            $arr = $_POST;
        foreach ($fields as $field) {
            if (!isset($arr[$field]))
                return false;
        }
        return true;
    }

    // Formatte une date, donnée dans le format YYYY-MM-DD, au format d'affichage DD/MM/YYYY
    public static function format_date($date) {
        return $date === null ? '' : (new DateTime($date))->format('d/m/Y');
    }

    public static function check_string_length($str, $min, $max) {
        $len = strlen(trim($str));
        return $len >= $min && $len <= $max;
    }

    public static function is_valid_date($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

}
