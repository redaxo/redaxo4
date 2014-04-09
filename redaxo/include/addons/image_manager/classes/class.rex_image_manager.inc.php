<?php

class rex_image_manager
{
    var $image_cacher;

    public function rex_image_manager(rex_image_cacher $image_cacher)
    {
        if (!rex_image_cacher::isValid($image_cacher)) {
            trigger_error('Given cache is not a valid rex_image_cacher', E_USER_ERROR);
        }
        $this->image_cacher = $image_cacher;
    }

    public function applyEffects(rex_image $image, $type)
    {
        global $REX;

        if (!$this->image_cacher->isCached($image, $type)) {
            $set = $this->effectsFromType($type);

            // REGISTER EXTENSION POINT
            $set   = rex_register_extension_point('IMAGE_MANAGER_FILTERSET', $set, array('rex_image_type' => $type, 'img' => $image));

            $image->prepare();

            // execute effects on image
            $effect = array();
            $c = 1;
            foreach ($set as $effect_params) {

                $effect_class = 'rex_effect_' . $effect_params['effect'];
                require_once dirname(__FILE__) . '/effects/class.' . $effect_class . '.inc.php';

                $effect[$c] = new $effect_class;
                $effect[$c]->setImage($image);
                $effect[$c]->setParams($effect_params['params']);
                if (!$effect[$c]->execute()) {
                } else {
                }

            }

            if (!rex_image::isValid($image) || !$image->isImage() ) {
                // Given image is not a valid rex_image
                $image->sendErrorImage();
            }

        }

        return $image;
    }

    public function effectsFromType($type)
    {
        global $REX;

        $qry = '
            SELECT e.*
            FROM ' . $REX['TABLE_PREFIX'] . '679_types t, ' . $REX['TABLE_PREFIX'] . '679_type_effects e
            WHERE e.type_id = t.id AND t.name="' . $type . '" order by e.prior';

        $sql = rex_sql::factory();
        $sql->setQuery($qry);

        $effects = array();
        while ($sql->hasNext()) {
            $effname = $sql->getValue('effect');
            $params = unserialize($sql->getValue('parameters'));
            $effparams = array();

            // extract parameter out of array
            if (isset($params['rex_effect_' . $effname])) {
                foreach ($params['rex_effect_' . $effname] as $name => $value) {
                    $effparams[str_replace('rex_effect_' . $effname . '_', '', $name)] = $value;
                    unset($effparams[$name]);
                }
            }

            $effect = array(
                'effect' => $effname,
                'params' => $effparams,
            );

            $effects[] = $effect;
            $sql->next();
        }
        return $effects;
    }

    /**
     * Returns a rex_image instance representing the image $rex_img_file
     * in respect to $rex_img_type.
     * If the result is not cached, the cache will be created.
     */
    public static function getImageCache($rex_img_file, $rex_img_type)
    {
        global $REX;

        $imagepath = $REX['HTDOCS_PATH'] . $REX['MEDIA_DIR'] . '/' . $rex_img_file;
        $cachepath = $REX['GENERATED_PATH'] . '/files/';

        $image         = new rex_image($imagepath);
        $image_cacher  = new rex_image_cacher($cachepath);

        // create image with given image_type if needed
        if (!$image_cacher->isCached($image, $rex_img_type)) {
            $image_manager = new self($image_cacher);
            $image_manager->applyEffects($image, $rex_img_type);
            $image->save($image_cacher->getCacheFile($image, $rex_img_type));
        }

        return $image_cacher->getCachedImage($rex_img_file, $rex_img_type);
    }

    public function sendImage(rex_image $image, $type)
    {
        $this->image_cacher->sendImage($image, $type);
    }
}
