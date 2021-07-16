<?php
/**
 * DZCP - deV!L`z ClanPortal - Mainpage ( dzcp.de )
 * deV!L`z Clanportal ist ein Produkt von CodeKing,
 * ge�ndert d�rch my-STARMEDIA und Codedesigns.
 *
 * Diese Datei ist ein Bestandteil von dzcp.de
 * Diese Version wurde speziell von Lucas Brucksch (Codedesigns) f�r dzcp.de entworfen bzw. ver�ndert.
 * Eine Weitergabe dieser Datei au�erhalb von dzcp.de ist nicht gestattet.
 * Sie darf nur f�r die Private Nutzung (nicht kommerzielle Nutzung) verwendet werden.
 *
 * Homepage: http://www.dzcp.de
 * E-Mail: info@web-customs.com
 * E-Mail: lbrucksch@codedesigns.de
 * Copyright 2017 � CodeKing, my-STARMEDIA, Codedesigns
 */

/**
 * BBCodeParser Class expanded the NBBC: The New BBCode Parser
 */
class BBCode extends common
{
    private static $words = null;
    private static $string = null;
    private static $smileys = null;

    /**
     * BBCode constructor.
     * @param bool $reint
     */
    public function __construct(bool $reint=false) {
        if($reint) {
            //->ReInit-Nbbc_BBCode
            self::$BBCode = new Nbbc\BBCode();
        }

        //Add Smileys
        self::$BBCode->SetSmileyDir(basePath.'/vendor/nbbc/smileys'); //default
        self::$BBCode->SetSmileyURL('../vendor/nbbc/smileys');
        if(is_dir(basePath.'/inc/_templates_/'.common::$tmpdir.'/images/smileys')) { //Check Template
            $smileyadd = common::get_files(basePath.'/inc/_templates_/'.common::$tmpdir.'/images/smileys',false,true, common::SUPPORTED_PICTURE);
            if($smileyadd && count($smileyadd) >= 1) {
                self::$BBCode->SetSmileyDir(basePath . '/inc/_templates_/' . common::$tmpdir . '/images/smileys');
                self::$BBCode->SetSmileyURL(common::$designpath . '/images/smileys');
                self::$BBCode->ClearSmileys();
                foreach (self::$BBCode->GetDefaultSmileys() as $tag => $smiley) {
                    if (file_exists(basePath . '/inc/_templates_/' . common::$tmpdir . '/images/smileys/' . $smiley)) {
                        self::$BBCode->AddSmiley($tag, $smiley);
                    }
                } unset($tag, $smiley);

                //Add new Smileys from Template
                if ($smileys = common::get_files(basePath . '/inc/_templates_/' . common::$tmpdir . '/images/smileys/',false,true, common::SUPPORTED_PICTURE)) {
                    foreach ($smileys as $smiley) {
                        $smiley_file = strtolower($smiley);
                        $smiley_name = str_replace(['.gif', '.png', '.jpg'], '', $smiley_file);
                        if (file_exists(basePath . '/inc/_templates_/' . common::$tmpdir . '/images/smileys/'.$smiley_name.'.xml')) { //Load XML
                            $xml = simplexml_load_file(basePath . '/inc/_templates_/' . common::$tmpdir . '/images/smileys/' . $smiley_name . '.xml');
                            self::$smileys[$smiley_file] = ['description' => (string)$smiley_name, 'map' => (string)$xml->tag[0]];
                            foreach ($xml->tag as $tag) {
                                self::$BBCode->AddSmiley(':'.str_replace(' ','_',(string)$tag[0]).':', $smiley_file);
                            } unset($xml,$tag);
                        } else {
                            self::$smileys[$smiley_file] = ['description' => (string)$smiley_name, 'map' => ':'.(string)$smiley_name.':'];
                            self::$BBCode->AddSmiley(':'.str_replace(' ','_',$smiley_name).':', $smiley_file);
                        }
                    }
                    unset($smileys, $smiley);
                }
            }
        }

        //Add new BBCodes
        self::$BBCode->AddRule('border', [
            'mode' => Nbbc\BBCode::BBCODE_MODE_ENHANCED,
            'template' => '<div style="border: {$size}px solid {$color}">{$_content}</div>',
            'allow' => [
                'color' => '/^#[0-9a-fA-F]+|[a-zA-Z]+$/',
                'size' => '/^[1-9][0-9]*$/',
            ],
            'default' => [
                'color' => 'blue',
                'size' => '1',
            ],
            'class' => 'block',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
        ]);

        /*
         * ##############################
         * Youtube TAG
         * ##############################
         *
         * Usage:
         * [youtube]xxxxx[/youtube] or
         * [youtube height=200 width=300]xxxxx[/youtube] or
		 *  Options:
         * [youtube autoplay=1]xxxxx[/youtube]
		 * [youtube allowfullscreen=1]xxxxx[/youtube]
		 * [youtube nocookie=1]xxxxx[/youtube]
		 * [youtube rel=0]xxxxx[/youtube]
		 * [youtube controls=0]xxxxx[/youtube]
		 * [youtube responsive=1]xxxxx[/youtube]
         *
         * [youtube autoplay=1 allowfullscreen=1 nocookie=1 rel=0 controls=0 responsive=1 height=200 width=300]1MLRCczBKn8[/youtube]
         */
        self::$BBCode->AddRule('youtube', [
            'mode' => Nbbc\BBCode::BBCODE_MODE_CALLBACK,
            'method' => 'BBCode::callback_youtube',
            'class' => 'block',
            'allow_in' => ['listitem', 'block', 'columns', 'inline'],
        ]);

        /*
         * ##############################
         * DivX Player TAG
         * ##############################
         *
         * Usage:
         * [divx]http://xxx.xx/video123.divx[/divx] or
         * [divx height=200 width=300]http://xxx.xx/video123.divx[/divx] or
         * [divx height=200 width=300 autoplay=1]http://xxx.xx/video123.divx[/divx]
         */
        self::$BBCode->AddRule('divx', [
            'mode' => Nbbc\BBCode::BBCODE_MODE_CALLBACK,
            'method' => 'BBCode::callback_divx',
            'class' => 'block',
            'allow_in' => ['listitem', 'block', 'columns', 'inline'],
        ]);

        /*
         * ##############################
         * HTML 5 Video Player TAG
         * ##############################
         *
         * Usage:
         * [video]http://xxx.xx/video123.mp4[/video] or
         * [video height=200 width=300]http://xxx.xx/video123.mp4[/video] or
         * [video height=200 width=300 autoplay=1]http://xxx.xx/video123.mp4[/video]
         */
        self::$BBCode->AddRule('video', [
            'mode' => Nbbc\BBCode::BBCODE_MODE_CALLBACK,
            'method' => 'BBCode::callback_video',
            'class' => 'block',
            'allow_in' => ['listitem', 'block', 'columns', 'inline'],
        ]);

        /*
         * ##############################
         * Vimeo Player TAG
         * ##############################
         *
         * Usage:
         * [vimeo]xxxxxxxx[/vimeo] or
         * [vimeo height=200 width=300]xxxxxxxx[/vimeo] or
         * [vimeo height=200 width=300 autoplay=1]xxxxxxxx[/vimeo]
         */
        self::$BBCode->AddRule('vimeo', [
            'mode' => Nbbc\BBCode::BBCODE_MODE_CALLBACK,
            'method' => 'BBCode::callback_vimeo',
            'class' => 'block',
            'allow_in' => ['listitem', 'block', 'columns', 'inline'],
        ]);

        /*
         * ##############################
         * Golem Player TAG
         * ##############################
         *
         * Usage:
         * [golem]xxxxxxxx[/golem] or
         * [golem height=200 width=300]xxxxxxxx[/golem] or
         * [golem height=200 width=300 autoplay=1]xxxxxxxx[/golem]
         */
        self::$BBCode->AddRule('golem', [
            'mode' => Nbbc\BBCode::BBCODE_MODE_CALLBACK,
            'method' => 'BBCode::callback_golem',
            'class' => 'block',
            'allow_in' => ['listitem', 'block', 'columns', 'inline'],
        ]);

        /*
         * ##############################
         * Text Hiden TAG
         * ##############################
         *
         * Usage:
         * [hide]Text1234 show on >= level 1[/hide] or
         * [hide level=2]Text1234 show on >= level 2[/hide] or
         * [hide level=3]Text1234 show on >= level 3[/hide] or
         * [hide level=4]Text1234 show on == level 4[/hide]
         */
        self::$BBCode->AddRule('hide', [
            'mode' => Nbbc\BBCode::BBCODE_MODE_CALLBACK,
            'method' => 'BBCode::callback_hide',
            'class' => 'block',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
        ]);

        self::$BBCode->AddRule('size', [
            'mode' => Nbbc\BBCode::BBCODE_MODE_CALLBACK,
            'method' => 'BBCode::callback_size',
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
        ]);
    }

    /**
     * Youtube BBCode callback for NBBC
     * @param $bbcode
     * @param $action
     * @param $name
     * @param $default
     * @param $params
     * @param $content
     * @return bool|string
     */
    public static function callback_youtube($bbcode, $action, $name, $default, $params, $content) {
        if($name == 'youtube') {
            if ($action == Nbbc\BBCode::BBCODE_CHECK) {
                if (isset($params['height']) && !preg_match('/^[1-9][0-9]*$/', $params['height']))
                    return false;

                if (isset($params['width']) && !preg_match('/^[1-9][0-9]*$/', $params['width']))
                    return false;

                if (isset($params['autoplay']) && !preg_match('/^[0-1]*$/', $params['autoplay']))
                    return false;

                if (isset($params['start']) && !preg_match('/^[1-9][0-9]*$/', $params['start']))
                    return false;
				
				if (isset($params['allowfullscreen']) && !preg_match('/^[0-1]*$/', $params['allowfullscreen']))
                    return false;
				
				if (isset($params['nocookie']) && !preg_match('/^[0-1]*$/', $params['nocookie']))
                    return false;
				
				if (isset($params['rel']) && !preg_match('/^[0-1]*$/', $params['rel']))
                    return false;
				
				if (isset($params['controls']) && !preg_match('/^[0-1]*$/', $params['controls']))
                    return false;
				
				if (isset($params['responsive']) && !preg_match('/^[0-1]*$/', $params['responsive']))
                    return false;
				
                return true;
            }

            $responsiveStyle = ''; $responsive = ['start' => '', 'end' => ''];
            $width = isset($params['width']) ? $params['width'] : 640;
            $height = isset($params['height']) ? $params['height'] : 385;
			$nocookie = isset($params['nocookie']) ? 'youtube-nocookie' : 'youtube';
			
			//build_query 
			$build_query = [];
			if(isset($params['autoplay'])) {
				$build_query['autoplay'] = $params['autoplay'];
			}
			
			if(isset($params['start'])) {
				$build_query['start'] = (int)$params['start'];
			}
			
			if(isset($params['rel'])) {
				$build_query['rel'] = (int)$params['rel'];
			}
			
			if(isset($params['controls']))
				$build_query['controls'] = (int)$params['controls'];
			}
			
			if(isset($params['responsive'])) {
				$responsive['start'] = '<div class="youtube-embed-wrapper" style="position:relative;padding-bottom:56.25%;padding-top:30px;height:0;overflow:hidden">';
				$responsive['end'] = '</div>';
				$responsiveStyle = 'style="position:absolute;top:0;left:0;width:100%;height:100%"';
			}

			$query = http_build_query($build_query);
            return $responsive['start'].
			'<iframe class="youtube-player" type="text/html" width="' . $width . '" height="' .
                $height . '" src="https://www.'.$nocookie.'.com/embed/' .
				$content . (!empty($query) ? '?'.$query : ''). 
				'"'.$responsiveStyle.' frameborder="0"'.(isset($params['allowfullscreen']) ? ' allowfullscreen': '').'></iframe>'.
			$responsive['end'];

        return $content;
    }

    /**
     * DivX BBCode callback for NBBC
     * @param $bbcode
     * @param $action
     * @param $name
     * @param $default
     * @param $params
     * @param $content
     * @return bool|string
     */
    public static function callback_divx($bbcode, $action, $name, $default, $params, $content) {
        if($name == 'divx') {
            if ($action == Nbbc\BBCode::BBCODE_CHECK) {
                if (isset($params['height']) && !preg_match('/^[1-9][0-9]*$/', $params['height']))
                    return false;

                if (isset($params['width']) && !preg_match('/^[1-9][0-9]*$/', $params['width']))
                    return false;

                if (isset($params['autoplay']) && !preg_match('/^[0-1]*$/', $params['autoplay']))
                    return false;

                return true;
            }

            $width = isset($params['width']) ? $params['width'] : 640;
            $height = isset($params['height']) ? $params['height'] : 385;
            $autoplay = isset($params['autoplay']) ? $params['autoplay'] : 0;

            return '<object classid="clsid:'.common::guid().'" width="' . $width . '" height="' . $height . '" wmode="opaque" codebase="http://go.divx.com/plugin/DivXBrowserPlugin.cab">'
            . '<param name="custommode" value="none" /><param name="autoPlay" value="'.($autoplay ? 'true' : 'false').'" /><param name="src" value="'.$content.'" />'
            . '<embed type="video/divx" src="'.$content.'" custommode="none" width="' . $width . '" height="' . $height . '" autoPlay="'.($autoplay ? 'true' : 'false')
                .'" pluginspage="https://go.divx.com/plugin/download/"></embed></object>';
        }

        return $content;
    }

    /**
     * Video BBCode HTML 5 callback for NBBC
     * @param $bbcode
     * @param $action
     * @param $name
     * @param $default
     * @param $params
     * @param $content
     * @return bool|string
     */
    public static function callback_video($bbcode, $action, $name, $default, $params, $content) {
        if($name == 'video') {
            if ($action == Nbbc\BBCode::BBCODE_CHECK) {
                if (isset($params['height']) && !preg_match('/^[1-9][0-9]*$/', $params['height']))
                    return false;

                if (isset($params['width']) && !preg_match('/^[1-9][0-9]*$/', $params['width']))
                    return false;

                if (isset($params['autoplay']) && !preg_match('/^[0-1]*$/', $params['autoplay']))
                    return false;

                return true;
            }

            $width = isset($params['width']) ? $params['width'] : 320;
            $height = isset($params['height']) ? $params['height'] : 240;
            $autoplay = isset($params['autoplay']) ? $params['autoplay'] : 0;

            return '<video width="'.$width.'" height="'.$height.'" controls preload="metadata"'.($autoplay ? ' autoplay' : '')
                .'><source src="'.$content.'" type="video/mp4">'._error_no_html5_vid.'</video>';
        }

        return $content;
    }

    /**
     * Vimeo BBCode callback for NBBC
     * @param $bbcode
     * @param $action
     * @param $name
     * @param $default
     * @param $params
     * @param $content
     * @return bool|string
     */
    public static function callback_vimeo($bbcode, $action, $name, $default, $params, $content) {
        if($name == 'vimeo') {
            if ($action == Nbbc\BBCode::BBCODE_CHECK) {
                if (isset($params['height']) && !preg_match('/^[1-9][0-9]*$/', $params['height']))
                    return false;

                if (isset($params['width']) && !preg_match('/^[1-9][0-9]*$/', $params['width']))
                    return false;

                if (isset($params['autoplay']) && !preg_match('/^[0-1]*$/', $params['autoplay']))
                    return false;

                return true;
            }

            $width = isset($params['width']) ? $params['width'] : 640;
            $height = isset($params['height']) ? $params['height'] : 297;
            $autoplay = isset($params['autoplay']) ? $params['autoplay'] : 0;

            return '<iframe src="https://player.vimeo.com/video/'.$content.'?autoplay='.
                ($autoplay ? '1' : '0').'&color=ffffff" width="'.$width.'" height="'.$height.
                '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        }

        return $content;
    }

    /**
     * Golem.de BBCode callback for NBBC
     * @param $bbcode
     * @param $action
     * @param $name
     * @param $default
     * @param $params
     * @param $content
     * @return bool|string
     */
    public static function callback_golem($bbcode, $action, $name, $default, $params, $content) {
        if($name == 'golem') {
            if ($action == Nbbc\BBCode::BBCODE_CHECK) {
                if (isset($params['height']) && !preg_match('/^[1-9][0-9]*$/', $params['height']))
                    return false;

                if (isset($params['width']) && !preg_match('/^[1-9][0-9]*$/', $params['width']))
                    return false;

                if (isset($params['autoplay']) && !preg_match('/^[0-1]*$/', $params['autoplay']))
                    return false;

                return true;
            }

            $width = isset($params['width']) ? $params['width'] : 480;
            $height = isset($params['height']) ? $params['height'] : 270;
            $autoplay = isset($params['autoplay']) ? $params['autoplay'] : 0;

            return "<object width=\"".$width."\" height=\"".$height."\" wmode=\"opaque\"></param><param name=\"wmode\" value=\"opaque\">"
            . "<param name=\"movie\" value=\"https://video.golem.de/player/videoplayer.swf?id=".$content."&autoPl=".($autoplay ? 'true' : 'false')."\"><param name=\"allowFullScreen\" value=\"true\">"
            . "<param name=\"AllowScriptAccess\" value=\"always\"><embed src=\"https://video.golem.de/player/videoplayer.swf?id=".$content."&autoPl=".($autoplay ? 'true' : 'false')."\" "
            . "type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" AllowScriptAccess=\"always\" width=\"".$width."\" height=\"".$height."\"></embed></object>";
        }

        return $content;
    }

    /**
     * Hide BBCode Tag callback for NBBC
     * @param $bbcode
     * @param $action
     * @param $name
     * @param $default
     * @param $params
     * @param $content
     * @return bool|string
     */
    public static function callback_hide($bbcode, $action, $name, $default, $params, $content) {
        if($name == 'hide') {
            if ($action == Nbbc\BBCode::BBCODE_CHECK) {
                if (isset($params['level']) && !preg_match('/^[1-9]*$/', $params['level']))
                    return false;

                return true;
            }

            $level = isset($params['level']) ? $params['level'] : 1;
            return common::$chkMe >= $level ? $content : '';
        }

        return $content;
    }

    /**
     * Format a [size] tag by producing a <span> with a style with a different font-size.
     *
     * @param BBCode $bbcode The {@link BBCode} object doing the parsing.
     * @param int $action The current action being performed on the tag.
     * @param string $name The name of the tag.
     * @param string $default The default value passed to the tag in the form: `[tag=default]`.
     * @param array $params All of the parameters passed to the tag.
     * @param string $content The content of the tag. Only available when {@link $action} is **BBCODE_OUTPUT**.
     * @return string Returns a span with the font size CSS.
     */
    public static function callback_size($bbcode, $action, $name, $default, $params, $content) {
        switch ($default) {
            case '80':
                $size = '.80em';
                break;
            case '120':
                $size = '1.20em';
                break;
            case '150':
                $size = '1.5em';
                break;
            case '200':
                $size = '2.0em';
                break;
            case '300':
                $size = '3.0em';
                break;
            case '400':
                $size = '4.0em';
                break;
            case '500':
                $size = '5.0em';
                break;
            default:
                $size = '1.0em';
                break;
        }

        return '<span style="font-size:'.$size.'">'.$content.'</span>';
    }

    /*
     * ##################################
     * Side Functions
     * ##################################
     */

    /**
     * Badword Filter
     */
    private static function badword_filter() {
        if(empty(self::$words) || !is_array(self::$words)) {
            self::$words = trim(settings::get('badwords', true));
            if(empty(self::$words)) return;
            self::$words = explode(",",self::$words);
        }

        if(count(self::$words) >= 1) {
            foreach(self::$words as $word) {
                self::$string = preg_replace_callback("#".$word."#i",
                    create_function('$matches','return str_repeat("*", strlen($matches[0]));'),self::$string);
            } unset($word);
        }
    }

    /**
     * BBCodes in HTML Tags umwandeln
     * @param string $input
     * @param bool $decode
     * @return string
     */
    public static function parse_html(string $input,bool $decode = true) {
        self::$string = ($decode ? stringParser::decode($input) : $input);
        unset($input);

        //Check of empty input
        if(empty(self::$string))
            return self::$string;

        //Filter Badwords
        self::badword_filter();

        //Use BBCode
        return self::getInstance()->Parse(self::$string);
    }

    /**
     * Textteil in ein Zitat setzen * blockquote *
     * @param string $nick,string $zitat,
     * @return string (html-code)
     */
    public static function zitat($nick,$zitat) {
        $search  = [chr(145),chr(146),"'",chr(147),chr(148),chr(10),chr(13)];
        $replace = [chr(39),chr(39),"&#39;",chr(34),chr(34)," "," "];
        $zitat = preg_replace("#[\n\r]+#", "<br />", str_replace($search, $replace, $zitat));
        return '<br /><br /><br /><blockquote><b>'.$nick.' '._wrote.':</b><br />'.stringParser::decode($zitat).'</blockquote>';
    }

    public static function nletter($txt)
    { return '<style type="text/css">p { margin: 0px; padding: 0px; }</style>'.$txt; }


    /**
     * @param string $txt
     * @return mixed
     */
    public static function bbcode_email(string $txt) {
        return str_replace(["&#91;","&#93;"],
            ["[","]"],self::parse_html((string)$txt));
    }

    /**
     * Get NBBC Instance
     * @return \Nbbc\BBCode
     */
    public static final function getInstance() {
        if (self::$BBCode instanceof Nbbc\BBCode) {
            return self::$BBCode;
        }

        self::__construct(true);
        return self::$BBCode;
    }

    public static function smiley_map($reint=false): array
    {
        if (count(self::$smileys) >= 1 || $reint) {
            $smileys = ['smiley_images' => [], 'smiley_descriptions' => [], 'smiley_map' => []];
            foreach (self::$smileys as $image => $data) {
                array_push($smileys['smiley_images'],$image);
                array_push($smileys['smiley_descriptions'],$data['description']);
                array_push($smileys['smiley_map'],[$data['description']] = $data['map']);
            }

            return $smileys;
        }

        self::__construct(true);
        return self::smiley_map(true);
    }
}