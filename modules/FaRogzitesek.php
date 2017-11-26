<?php

abstract class FaRogzitesek extends BaseModule
{
    public static $version = "1.0.0";

    static public function get($params)
    {
        $req = self::expectedParameters($params, ['neptun_kod', 'szektor_id', 'utca_id', 'felev']);
        if (!$req[0]) {
            $info = array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'To use API, please read the documentation.'
            );
            JSON::set(get_class(), $info, self::$version);
            return;
        }

        $perm = AuthModel::checkPermission($params);
        if ($perm == -1) {
            $info = array(
                'code' => -1,
                'message' => "The authentication data of user is incorrect. Please log in again!"
            );
        } else if ($perm >= 1) {
            //TODO(Alex): Should implement the Listing to Admins
        } else {
            if (!empty($params["id"])) {
                $list = R::getAll('SELECT fr.*, u.nev, ff.kategoria, ff.magyar, ff.latin FROM FaRogzitesek fr INNER JOIN Users u ON u.neptun=fr.neptun_kod INNER JOIN FaFajok ff ON ff.id=fr.fa_fajta WHERE fr.fa_egyedi_id=? AND fr.szektor_id=? AND fr.utca_id=? AND fr.felev=? ORDER BY fr.fa_egyedi_id DESC', [
                    $params["id"], $params["szektor_id"], $params["utca_id"], $params["felev"]
                ]);

                $plist = [];
                foreach ($list as $item) {
                    array_push($plist, $item);
                }
                $info = array(
                    'code' => 1,
                    'message' => "Listing instances of " . $params["felev"] . " semester of " . $params["szektor_id"] . "" . $params["utca_id"] . " trees",
                    'instance' => $plist[0]
                );

            } else {
                $list = R::getAll('SELECT fr.*, u.nev FROM FaRogzitesek fr INNER JOIN Users u ON u.neptun=fr.neptun_kod WHERE fr.szektor_id=? AND fr.utca_id=? AND fr.felev=? ORDER BY fr.fa_egyedi_id DESC', [
                    $params["szektor_id"], $params["utca_id"], $params["felev"]
                ]);
                $plist = [];
                foreach ($list as $item) {
                    array_push($plist, $item);
                }
                $info = array(
                    'code' => 1,
                    'message' => "Listing instances of " . $params["felev"] . " semester of " . $params["szektor_id"] . "" . $params["utca_id"] . " trees",
                    'list' => $plist
                );
            }
        }
        JSON::set(get_class(), $info, self::$version);
    }

    static public function post($params)
    {
        $req = self::expectedParameters($params, [
            'auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev',
            'piszkozat', 'hazszam', 'tipus', 'faFajta', 'lat', 'lng', 'acc', 'magassag', 'holtmagassag',
            'image1b64', 'image2b64',
            'koronaAllapota', 'koronaForma', 'koronaFormaEgyeb', 'koronaSugar', 'koronaban',
            'koronaAlapnal', 'koronaHiany', 'koronaElhalt', 'koronaFeny', 'torzsAllapota',
            'torzsSzama', 'torzsAlul', 'torzsKeruletek', 'torzsMagassag', 'torzson', 'gyokernyakon',
            'egyeb', 'faHelyJellemzo', 'faHelyJellemzoEgyeb', 'faHelyMeret', 'faHelyJellemzoFaveremracsAtmero',
            'kozeliEpulet', 'favedelem', 'favedelemEgyeb', 'kozmuvek', 'faAllapota', 'ajanlott'
        ]);
        if (!$req[0]) {
            $info = array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'To use API, please read the documentation.'
            );
            JSON::set(get_class(), $info, self::$version);
            return;
        }
        $image1B64 = $params["image1b64"];
        $image2B64 = $params["image2b64"];
        unset($params["image1b64"]);
        unset($params["image2b64"]);

        $count = ((int)@(R::getAll('SELECT * FROM FaRogzitesek WHERE szektor_id=? AND utca_id=? AND felev=? ORDER BY fa_egyedi_id DESC LIMIT 1', [
                $params["szektor_id"], $params["utca_id"], $params["felev"]
            ])[0])["fa_egyedi_id"]) + 1;

        $count = $count < 10 ? "00" . $count : ($count < 100 ? "0" . $count : $count);
        $k1 = self::base64_to_jpeg($params["szektor_id"] . $params["utca_id"] . $count, 1, $params["felev"], $image1B64);
        $k2 = self::base64_to_jpeg($params["szektor_id"] . $params["utca_id"] . $count, 2, $params["felev"], $image2B64);
        $k1 = $k1 != false ? $k1 : null;
        $k2 = $k2 != false ? $k2 : null;

        R::exec("INSERT INTO `FaRogzitesek` (`neptun_kod`, `szektor_id`, `utca_id`, `felev`,
            `fa_egyedi_id`, `createdAt`, `piszkozat`, `hazszam`, `tipus`, `fa_fajta`, `lat`, `lng`, `acc`, `magassag`, `holtmagassag`,
            `koronaAllapota`, `koronaForma`, `koronaFormaEgyeb`, `koronaSugar`, `koronaban`,
            `koronaAlapnal`, `koronaHiany`, `koronaElhalt`, `koronaFeny`, `torzsAllapota`,
            `torzsSzama`, `torzsAlul`, `torzsKeruletek`, `torzsMagassag`, `torzson`, `gyokernyakon`,
            `egyeb`, `faHelyJellemzo`, `faHelyJellemzoEgyeb`, `faHelyMeret`, `faHelyJellemzoFaveremracsAtmero`,
            `kozeliEpulet`, `favedelem`, `favedelemEgyeb`, `kozmuvek`, `faAllapota`, `ajanlott`, `image1`, `image2`) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )",
            [
                $params["neptun_kod"], $params["szektor_id"], $params["utca_id"], $params["felev"], $count,
                time(), $params["piszkozat"], $params["hazszam"], $params["tipus"], $params["faFajta"],
                $params["lat"], $params["lng"], $params["acc"], $params["magassag"], $params["holtmagassag"],
                $params["koronaAllapota"], $params["koronaForma"], $params["koronaFormaEgyeb"],
                $params["koronaSugar"], $params["koronaban"], $params["koronaAlapnal"], $params["koronaHiany"],
                $params["koronaElhalt"], $params["koronaFeny"], $params["torzsAllapota"], $params["torzsSzama"],
                $params["torzsAlul"], $params["torzsKeruletek"], $params["torzsMagassag"], $params["torzson"],
                $params["gyokernyakon"], $params["egyeb"], $params["faHelyJellemzo"], $params["faHelyJellemzoEgyeb"],
                $params["faHelyMeret"], $params["faHelyJellemzoFaveremracsAtmero"], $params["kozeliEpulet"],
                $params["favedelem"], $params["favedelemEgyeb"], $params["kozmuvek"], $params["faAllapota"],
                $params["ajanlott"], $k1, $k2
            ]
        );
        $info = array(
            'code' => 1,
            'message' => "The Tree has been added into the database",
            'treeid' => $count
        );


        if (DEBUG) JSON::set("debug", array($params));
        JSON::set(get_class(), $info, self::$version);
    }

    static public function delete($params)
    {
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev', 'faid', 'fafelev']);
        if (!$req[0]) {
            JSON::set(get_class(), array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")"
            ), self::$version);
            return;
        }
        $szektor = substr($params["patch_faid"], 0, 2);
        $utca = substr($params["patch_faid"], 2,3);
        $id = substr($params["patch_faid"], 5, 3);
        if ($params["szektor_id"] == $szektor && $params["utca_id"] == $utca && $params["felev"] == $params["fafelev"]) {
            $instance = @R::findOne(get_class(),
                ' szektor_id=? AND utca_id=? AND felev=? AND fa_egyedi_id=?',
                [$params["szektor_id"], $params["utca_id"], $params["felev"], $id]);
            if ($instance != NULL) {
                if (!empty($instance->image1)) unlink($instance->image1);
                if (!empty($instance->image2)) unlink($instance->image2);
                R::exec("DELETE FROM " . get_class() . " WHERE szektor_id=? AND utca_id=? AND felev=? AND fa_egyedi_id=?",
                    [
                        $params["szektor_id"], $params["utca_id"], $params["felev"], $id
                    ]);

                $info = array(
                    'code' => 1,
                    'message' => "The instance of " . get_class() . " has been deleted from the database."
                );
            } else {
                $info = array(
                    'code' => 0,
                    'message' => "The instance of " . get_class() . " is not found, so we can not delete."
                );
            }
        } else {
            $perm = AuthModel::checkPermission($params);
            if ($perm == -1) {
                $info = array(
                    'code' => -1,
                    'message' => "The authentication data of user is incorrect. Please log in again!"
                );
            } else if ($perm >= 1) {
                $instance = @R::findOne(get_class(),
                    ' szektor_id=? AND utca_id=? AND felev=? AND fa_egyedi_id=?',
                    [$params["szektor_id"], $params["utca_id"], $params["felev"], $id]);
                if ($instance == NULL) {
                    $info = array(
                        'code' => 404,
                        'message' => "The instance of " . get_class() . " is not found, so we can not delete."
                    );
                } else {
                    if (!empty($instance->image1)) unlink($instance->image1);
                    if (!empty($instance->image2)) unlink($instance->image2);
                    R::exec("DELETE FROM " . get_class() . " WHERE szektor_id=? AND utca_id=? AND felev=? AND fa_egyedi_id=?",
                        [
                            $params["szektor_id"], $params["utca_id"], $params["felev"], $id
                        ]);
                    $info = array(
                        'code' => 200,
                        'message' => "The instance of " . get_class() . " has been deleted from the database."
                    );
                }
            } else {
                $info = array(
                    'code' => 0,
                    'message' => "You do not have enough permission for this."
                );
            }
        }
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set(get_class(), $info, self::$version);
    }

    static public function patch($params)
    {
        $req = self::expectedParameters($params, [
            'auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev',
            'patch_faid', 'patch_felev',
            'piszkozat', 'hazszam', 'tipus', 'faFajta', 'lat', 'lng', 'acc', 'magassag', 'holtmagassag',
            'koronaAllapota', 'koronaForma', 'koronaFormaEgyeb', 'koronaSugar', 'koronaban',
            'koronaAlapnal', 'koronaHiany', 'koronaElhalt', 'koronaFeny', 'torzsAllapota',
            'torzsSzama', 'torzsAlul', 'torzsKeruletek', 'torzsMagassag', 'torzson', 'gyokernyakon',
            'egyeb', 'faHelyJellemzo', 'faHelyJellemzoEgyeb', 'faHelyMeret', 'faHelyJellemzoFaveremracsAtmero',
            'kozeliEpulet', 'favedelem', 'favedelemEgyeb', 'kozmuvek', 'faAllapota', 'ajanlott'
        ]);
        if (!$req[0]) {
            $info = array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'To use API, please read the documentation.'
            );
            JSON::set(get_class(), $info, self::$version);
            return;
        }
        $szektor = substr($params["patch_faid"], 0, 2);
        $utca = substr($params["patch_faid"], 2,3);
        $id = substr($params["patch_faid"], 5, 3);
        $instance = @R::findOne(get_class(),
            ' szektor_id=? AND utca_id=? AND felev=? AND fa_egyedi_id=?',
            [$szektor, $utca, $params["patch_felev"], $id]);
        if ($instance != NULL) {
            if ($params["szektor_id"] == $szektor && $params["utca_id"] == $utca && $params["felev"] == $params["patch_felev"]) {
                R::exec("UPDATE `FaRogzitesek` SET `piszkozat`=?, `hazszam`=?, `tipus`=?, `fa_fajta`=?, `lat`=?,
                `lng`=?, `acc`=?, `magassag`=?, `holtmagassag`=?, `koronaAllapota`=?, `koronaForma`=?,
                `koronaFormaEgyeb`=?, `koronaSugar`=?, `koronaban`=?, `koronaAlapnal`=?, `koronaHiany`=?,
                `koronaElhalt`=?, `koronaFeny`=?, `torzsAllapota`=?,
                `torzsSzama`=?, `torzsAlul`=?, `torzsKeruletek`=?, `torzsMagassag`=?, `torzson`=?, `gyokernyakon`=?,
                `egyeb`=?, `faHelyJellemzo`=?, `faHelyJellemzoEgyeb`=?, `faHelyMeret`=?, `faHelyJellemzoFaveremracsAtmero`=?,
                `kozeliEpulet`=?, `favedelem`=?, `favedelemEgyeb`=?, `kozmuvek`=?, `faAllapota`=?, `ajanlott`=?
                WHERE 
                szektor_id=? AND utca_id=? AND felev=? AND fa_egyedi_id=?
                ",
                    [
                        $params["piszkozat"], $params["hazszam"], $params["tipus"], $params["faFajta"],
                        $params["lat"], $params["lng"], $params["acc"], $params["magassag"], $params["holtmagassag"],
                        $params["koronaAllapota"], $params["koronaForma"], $params["koronaFormaEgyeb"],
                        $params["koronaSugar"], $params["koronaban"], $params["koronaAlapnal"], $params["koronaHiany"],
                        $params["koronaElhalt"], $params["koronaFeny"], $params["torzsAllapota"], $params["torzsSzama"],
                        $params["torzsAlul"], $params["torzsKeruletek"], $params["torzsMagassag"], $params["torzson"],
                        $params["gyokernyakon"], $params["egyeb"], $params["faHelyJellemzo"], $params["faHelyJellemzoEgyeb"],
                        $params["faHelyMeret"], $params["faHelyJellemzoFaveremracsAtmero"], $params["kozeliEpulet"],
                        $params["favedelem"], $params["favedelemEgyeb"], $params["kozmuvek"], $params["faAllapota"],
                        $params["ajanlott"], $szektor, $utca, $params["patch_felev"], $id
                    ]
                );
                $info = array(
                    'code' => 200,
                    'message' => "The instance of " . get_class() . " has been updated."
                );
            } else {
                $perm = AuthModel::checkPermission($params);
                if ($perm == -1) {
                    $info = array(
                        'code' => -1,
                        'message' => "The authentication data of user is incorrect. Please log in again!"
                    );
                } else if ($perm >= 1) {
                    R::exec("UPDATE `FaRogzitesek` SET `piszkozat`=?, `hazszam`=?, `tipus`=?, `fa_fajta`=?, `lat`=?,
                `lng`=?, `acc`=?, `magassag`=?, `holtmagassag`=?, `koronaAllapota`=?, `koronaForma`=?,
                `koronaFormaEgyeb`=?, `koronaSugar`=?, `koronaban`=?, `koronaAlapnal`=?, `koronaHiany`=?,
                `koronaElhalt`=?, `koronaFeny`=?, `torzsAllapota`=?,
                `torzsSzama`=?, `torzsAlul`=?, `torzsKeruletek`=?, `torzsMagassag`=?, `torzson`=?, `gyokernyakon`=?,
                `egyeb`=?, `faHelyJellemzo`=?, `faHelyJellemzoEgyeb`=?, `faHelyMeret`=?, `faHelyJellemzoFaveremracsAtmero`=?,
                `kozeliEpulet`=?, `favedelem`=?, `favedelemEgyeb`=?, `kozmuvek`=?, `faAllapota`=?, `ajanlott`=?
                WHERE 
                szektor_id=? AND utca_id=? AND felev=? AND fa_egyedi_id=?
                ",
                        [
                            $params["piszkozat"], $params["hazszam"], $params["tipus"], $params["faFajta"],
                            $params["lat"], $params["lng"], $params["acc"], $params["magassag"], $params["holtmagassag"],
                            $params["koronaAllapota"], $params["koronaForma"], $params["koronaFormaEgyeb"],
                            $params["koronaSugar"], $params["koronaban"], $params["koronaAlapnal"], $params["koronaHiany"],
                            $params["koronaElhalt"], $params["koronaFeny"], $params["torzsAllapota"], $params["torzsSzama"],
                            $params["torzsAlul"], $params["torzsKeruletek"], $params["torzsMagassag"], $params["torzson"],
                            $params["gyokernyakon"], $params["egyeb"], $params["faHelyJellemzo"], $params["faHelyJellemzoEgyeb"],
                            $params["faHelyMeret"], $params["faHelyJellemzoFaveremracsAtmero"], $params["kozeliEpulet"],
                            $params["favedelem"], $params["favedelemEgyeb"], $params["kozmuvek"], $params["faAllapota"],
                            $params["ajanlott"], $szektor, $utca, $params["patch_felev"], $id
                        ]
                    );
                    $info = array(
                        'code' => 200,
                        'message' => "The instance of " . get_class() . " has been updated as moderator."
                    );
                } else {
                    $info = array(
                        'code' => 0,
                        'message' => "You do not have enough permission for this."
                    );
                }
            }
        } else {
            $info = array(
                'code' => 404,
                'message' => "The instance of " . get_class() . " is not found, so we can not patch."
            );
        }
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set(get_class(), $info, self::$version);
    }

    private static function base64_to_jpeg($sorszam, $iid, $felev, $base64_string)
    {
        $felev = str_replace("/", "-", $felev);
        if (!is_dir('images/' . $felev)) {
            mkdir('images/' . $felev, 0777, true);
        }
        $fname = "images/" . $felev . "/" . $sorszam . "_" . $iid . ".jpg";
        @unlink($fname);
        if (!empty($base64_string) && $base64_string != null) {
            $ifp = fopen($fname, 'wb');
            $data = explode(',', $base64_string);
            fwrite($ifp, base64_decode($data[1]));
            fclose($ifp);
            return $fname;
        }
        return false;
    }
}