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

    public static function check_string_length($str, $min, $max) {
        $len = strlen(trim($str));
        return $len >= $min && $len <= $max;
    }

    //indique si un mot de passe correspond à son hash
    public static function check_password($clear_password, $hash) {
        return $hash === Tools::my_hash($clear_password);
    }

    /* ======================================= */
    /* ===  Fonctions de gestion des dates === */
    /* ======================================= */

    // Formatte une date, donnée dans le format YYYY-MM-DD, au format d'affichage DD/MM/YYYY
    public static function format_date($date) {
        return $date === null ? '' : (new DateTime($date))->format('d/m/Y');
    }

    public static function format_datetime($date) {
        return $date === null ? '' : (new DateTime($date))->format('d/m/Y H:i:s');
    }
    
    public static function format_datetimBD($date){
         return $date === null ? '' : (new DateTime($date))->format('Y/m/d H:i:s');
    }

    public static function is_valid_date($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function get_date($str) {
        $ts = strtotime($str);
        $d = new DateTime();
        $d->setTimestamp($ts);
        return $d->format('Y-m-d');
    }

    public static function get_datetime($str) {
        $ts = strtotime($str);
        $d = new DateTime();
        $d->setTimestamp($ts);
        return $d->format('Y-m-d H:i:s');
    }

    public static function getTodayDateTime() {
        return date('d/m/Y H:i:s');
    }

    public static function getTodayDateTimeBdd() {
        return date('Y/m/d H:i:s');
    }

    /* ======================================= */
    /* === Fonctions de gestion des photos === */
    /* ======================================= */

    public static function validate_path() {
        if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
            if ($_FILES['image']['error'] == 0) {
                $typeOK = TRUE;

                if ($_FILES['image']['type'] == "image/gif")
                    $saveTo = $user . ".gif";
                else if ($_FILES['image']['type'] == "image/jpeg")
                    $saveTo = $user . ".jpg";
                else if ($_FILES['image']['type'] == "image/png")
                    $saveTo = $user . ".png";
                else {
                    $typeOK = FALSE;
                    $error = "Unsupported image format : gif, jpeg ou png !";
                }
            }
        }
    }

    //renvoie un tableau d'erreur(s) 
    //le tableau est vide s'il n'y a pas d'erreur.
    public static function validate_photo($file) {
        $errors = [];
        if (isset($file['name']) && $file['name'] != '') {
            if ($file['error'] == 0) {
                $valid_types = array("image/gif", "image/jpeg", "image/png");
                if (!in_array($_FILES['picture']['type'], $valid_types)) {
                    $errors[] = "Unsupported image format : gif, jpg/jpeg or png.";
                }
            } else {
                $errors[] = "Error while uploading file.";
            }
        }
        return $errors;
    }

    //pre : validate_photo($file) returns true
    public static function generate_photo_name($file) {
        //note : time() est utilisé pour que la nouvelle image n'aie pas
        //       le meme nom afin d'éviter que le navigateur affiche
        //       une ancienne image présente dans le cache
        if ($_FILES['picture']['type'] == "image/gif") {
            $saveTo = $title . time() . ".gif";
        } else if ($_FILES['picture']['type'] == "image/jpeg") {
            $saveTo = $title . time() . ".jpg";
        } else if ($_FILES['picture']['type'] == "image/png") {
            $saveTo = $title . time() . ".png";
        }
        return $saveTo;
    }

    /* ======================================= */
    /* ===       Fonctions d'encodage ===      */
    /* ======================================= */

    /**

     * Permet d'encoder une string au format base64url, c'est-à-dire un format base64 dans 

     * lequel les caractères '+' et '/' sont remplacés respectivement par '-' et '_', ce qui

     * permet d'utiliser le résultat dans un URL.

     *

     * @param string $data La string à encoder.

     * @return string La string encodée.

     */
    private static function base64url_encode($data) {

        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**

     * Permet de décoder une string encodée au format base64url.

     *

     * @param string $data La string à décoder.

     * @return string La string décodée.

     */
    private static function base64url_decode($data) {

        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }

    /**

     * Permet d'encoder une structure de donnée (par exemple un tableau associatif ou un

     * objet) au format base64url.

     *

     * @param mixed $data La structure de données à encoder.

     * @return string La string résultant de l'encodage.

     */
    public static function url_safe_encode($data) {

        return self::base64url_encode(gzcompress(json_encode($data), 9));
    }

    /**

     * Permet d'encoder une structure de donnée (par exemple un tableau associatif ou un

     * objet) au format base64url.

     *

     * @param mixed $data La structure de données à encoder.

     * @return string La string résultant de l'encodage.

     */
    public static function url_safe_decode($data) {

        return json_decode(@gzuncompress(self::base64url_decode($data)), true, 512, JSON_OBJECT_AS_ARRAY);
    }

}
