<?php
/***************************************************************************
 *  Copyright (C) 2003-2011 Polytechnique.org                              *
 *  http://opensource.polytechnique.org/                                   *
 *                                                                         *
 *  This program is free software; you can redistribute it and/or modify   *
 *  it under the terms of the GNU General Public License as published by   *
 *  the Free Software Foundation; either version 2 of the License, or      *
 *  (at your option) any later version.                                    *
 *                                                                         *
 *  This program is distributed in the hope that it will be useful,        *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of         *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          *
 *  GNU General Public License for more details.                           *
 *                                                                         *
 *  You should have received a copy of the GNU General Public License      *
 *  along with this program; if not, write to the Free Software            *
 *  Foundation, Inc.,                                                      *
 *  59 Temple Place, Suite 330, Boston, MA  02111-1307  USA                *
 ***************************************************************************/

class UrlShortenerModule extends PLModule
{
    function handlers()
    {
        return array(
            'url'       => $this->make_hook('url',       AUTH_COOKIE),
            'admin/url' => $this->make_hook('admin_url', AUTH_MDP, 'admin')
        );
    }

    function handler_url($page, $alias)
    {
        $url = XDB::fetchOneCell('SELECT  url
                                    FROM  url_shortener
                                   WHERE  alias = {?}',
                                 $alias);

        if (is_null($url)) {
            return PL_NOT_FOUND;
        }
        http_redirect($url);
    }

    function handler_admin_url($page)
    {
        $page->changeTpl('urlshortener/admin.tpl');

        if (!Post::has('url')) {
            return;
        }

        $url = Post::t('url');
        $alias = Post::t('alias');

        $url_regex = '{^(https?|ftp)://[a-zA-Z0-9._%#+/?=&~-]+$}i';
        if (!preg_match($url_regex, $url)) {
            $page->trigError("L'url donnée n'est pas valide.");
            return;
        }
        $page->assign('url', $url);

        if ($alias != '') {
            if (!preg_match('/^[a-zA-Z0-9\-]{6}$/i', $alias)) {
                $page->trigError("L'alias proposé n'est pas valide.");
                return;
            }
            $page->assign('alias', $alias);

            $used = XDB::fetchOneCell('SELECT  COUNT(*)
                                         FROM  url_shortener
                                        WHERE  alias = {?}',
                                      $alias);
            if ($used != 0) {
                $page->trigError("L'alias proposé est déjà utilisé.");
                return;
            }
        } else {
            do {
                $alias = rand_token(6);
                $used = XDB::fetchOneCell('SELECT  COUNT(*)
                                             FROM  url_shortener
                                            WHERE  alias = {?}',
                                          $alias);
            } while ($used != 0);
            $page->assign('alias', $alias);
        }

        XDB::execute('INSERT INTO  url_shortener (url, alias)
                           VALUES  ({?}, {?})',
                     $url, $alias);
        $page->trigSuccess("L'url « " . $url . ' » est maintenant accessible depuis « ' . Platal::globals()->baseurl . '/url/' . $alias . ' ».');
    }
}

// vim:set et sw=4 sts=4 sws=4 foldmethod=marker enc=utf-8:
?>