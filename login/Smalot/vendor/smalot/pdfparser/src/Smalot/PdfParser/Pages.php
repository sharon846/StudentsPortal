<?php

/**
 * @file
 *          This file is part of the PdfParser library.
 *
 * @author  Sébastien MALOT <sebastien@malot.fr>
 * @date    2017-01-03
 *
 * @license LGPLv3
 * @url     <http://github.com/smalot/pdfparser>
 *
 *  PdfParser is a pdf library written in PHP, extraction oriented.
 *  Copyright (C) 2017 - Sébastien MALOT <sebastien@malot.fr>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public License
 *  along with this program.
 *  If not, see <http://www.pdfparser.org/sites/default/LICENSE.txt>.
 */

namespace Smalot\PdfParser;

use Smalot\PdfParser\Element\ElementArray;

/**
 * Class Pages
 */
class Pages extends PDFObject
{
    /**
     * @todo Objects other than Pages or Page might need to be treated specifically in order to get Page objects out of them,
     *
     * @see http://github.com/smalot/pdfparser/issues/331
     */
    public function getPages(bool $deep = false): array
    {
        if (!$this->has('Kids')) {
            return [];
        }

        /** @var ElementArray $kidsElement */
        $kidsElement = $this->get('Kids');

        if (!$deep) {
            return $kidsElement->getContent();
        }

        $kids = $kidsElement->getContent();
        $pages = [];

        foreach ($kids as $kid) {
            if ($kid instanceof self) {
                $pages = array_merge($pages, $kid->getPages(true));
            } elseif ($kid instanceof Page) {
                $pages[] = $kid;
            }
        }

        return $pages;
    }
}