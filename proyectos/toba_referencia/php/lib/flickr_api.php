<?php
//
// PHP5 Flickr_API 
// Should be mostly API compatible with Cal Henderson's PEAR::Flickr_API
// but uses Curl and SimpleXML
//
// Rasmus Lerdorf, August 2005
//

class Flickr_API {
    private $_cfg = array('api_key'         => '',
                          'api_secret'      => '',
                          'endpoint'        => 'http://www.flickr.com/services/rest/',
                          'auth_endpoint'   => 'http://www.flickr.com/services/auth/?',
                          'upload_endpoint' => 'http://www.flickr.com/services/upload/',
                          'conn_timeout'    => 20,
                          'io_timeout'      => 60 );

    function __construct($params = array()) {
        if(isset($params['token'])) $this->token = $params['token'];
        foreach($params as $k => $v) {
            $this->_cfg[$k] = $v;
        }
        if(!$this->_cfg['api_key'] || !$this->_cfg['api_secret']) {
            throw new Exception("You must supply an api_key and an api_secret");
        }
    }

    function callMethod($method, $params = array()) {
        $this->_err_code = 0;
        $this->_err_msg = '';

        $req = curl_init();

        if($method=='upload') {
            $photo = $params['photo'];
            unset($params['photo']);
        } else {
            $params['method'] = $method;
        }
        $params['api_key'] = $this->_cfg['api_key'];
        $params['api_sig'] = $this->signArgs($params);

        if($method=='upload') {
            $params['photo'] = '@'.$photo;
            curl_setopt($req, CURLOPT_URL, $this->_cfg['upload_endpoint']);
            curl_setopt($req, CURLOPT_TIMEOUT, 0);
#            curl_setopt($req, CURLOPT_INFILESIZE, filesize($photo));
        } else {
            curl_setopt($req, CURLOPT_URL, $this->_cfg['endpoint']);
            curl_setopt($req, CURLOPT_TIMEOUT, $this->_cfg['io_timeout']);
            curl_setopt($req, CURLOPT_POST, 1);    
        }

        // Sign and build request parameters
        curl_setopt($req, CURLOPT_POSTFIELDS, $params);
        curl_setopt($req, CURLOPT_CONNECTTIMEOUT, $this->_cfg['conn_timeout']);
        curl_setopt($req, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($req, CURLOPT_HEADER, 0);
        curl_setopt($req, CURLOPT_RETURNTRANSFER, 1);
        $this->_http_body = curl_exec($req);

        if (curl_errno($req)) {
            throw new Exception(curl_error($req));
        }

        curl_close($req);
        //file_put_contents('/tmp/curl.log',print_r($params,true)."\n".$this->_http_body."\n\n",FILE_APPEND);

        $xml = simplexml_load_string($this->_http_body);
        $this->xml = $xml;

        if((string)$xml['stat'] == 'fail') {
            $this->_err_code = (int)$xml->err['code'];
            $this->_err_msg  = (string)$xml->err['msg'];
            return 0;
        }

        if ((string)$xml['stat'] != 'ok') {
            $this->_err_code = 0;
            $this->_err_msg = "Unrecognised REST response status";
            return 0;
        }

        return $xml;
    }

    function getErrorCode() {
        return $this->_err_code;
    }

    function getErrorMessage() {
        return $this->_err_msg;
    }

    function showError() {
        echo "<br />ErrorCode: {$this->_err_code}<br />ErrorMessage: {$this->_err_msg}<br />\n";
    }

    function getAuthUrl($perms, $frob='') {
        $args = array('api_key'=>$this->_cfg['api_key'],'perms'=>$perms);

        if (strlen($frob)) { $args['frob'] = $frob; }

        $args['api_sig'] = $this->signArgs($args);

        $fields =  '';
        foreach($args as $k => $v) {
            if($fields) $fields.='&';
            $fields .= urlencode($k).'='.urlencode($v);
        }

        return $this->_cfg['auth_endpoint'].$fields;
    }


    function signArgs($args){
        ksort($args);
        $a = '';
        foreach($args as $k => $v) {
            $a .= $k . $v;
        }
        return md5($this->_cfg['api_secret'].$a);
    }
}

class Flickr extends Flickr_API {
    function __construct($params = array()) {
        parent::__construct($params);
    }

    function getFrobToken($frob) {
        $xml = $this->callMethod('flickr.auth.getToken',array('frob'=>$frob));
        if(!$xml) { $this->showError(); return FALSE; }

        $this->token = $ret['token'] = (string)$xml->auth->token;
        $this->perms = $ret['perms'] = (string)$xml->auth->perms;
        $uattr = $xml->auth->user->attributes();
        foreach($xml->auth->user->attributes() as $k=>$v) {
            // Sets token,perms,nsid,username and fullname
            $this->$k = $ret[$k] = (string)$v;
        }        
        return $ret;
    }

    function getPhotoURL($p, $size='s', $ext='jpg') {
        return "http://static.flickr.com/{$p['server']}/{$p['id']}_{$p['secret']}_{$size}.{$ext}";
    }

    function authCheckToken($token) {
        $params = array('auth_token'  => $this->token);

        $xml = $this->callMethod('flickr.auth.checkToken',$params);
        if(!$xml) { return FALSE; }
        
        foreach($xml->auth->children() as $k=>$v) {
            $ret[(string)$k] = (string)$v;
            foreach($v->attributes() as $kk=>$vv) {
                $ret[(string)$kk] = (string)$vv;
            }    
        }
        return $ret;
    }
// Contacts

    function contactsGetList($filter='') {
        $params = array('auth_token'=>$this->token);
        if($filter) $params['filter'] = $filter;    

        $xml = $this->callMethod('flickr.contacts.getList',$params);
        if(!$xml) { return FALSE; }

        $i = 0;
        foreach($xml->contacts->contact as $contact) {
            foreach($contact->attributes() as $k=>$v) {
                $ret[$i][$k] = (string)$v;
            }
            $i++;
        }
        return $ret;
    }

    function contactsGetPublicList($id) {
        $params = array('user_id'=>$id);
        $xml = $this->callMethod('flickr.contacts.getPublicList',$params);
        if(!$xml) { return FALSE; }

        $i=0;
        foreach($xml->contacts->contact as $contact) {
            foreach($contact->attributes() as $k=>$v) {
                $ret[$i][$k] = (string)$v;
            }
            $i++;
        }
        return $ret;
    }

// Blogs

    function blogsGetList() {
        $params = array('auth_token'=>$this->token);
        $xml = $this->callMethod('flickr.blogs.getList',$params);
        if(!$xml) { return FALSE; }

        $i = 0;
        foreach($xml->blogs->blog as $blog) {
            foreach($blogs->attributes() as $k=>$v) {
                $ret[(string)$blog['id']][$k] = (string)$v;
            }
            $i++;
        }
        return $ret;
    }

    function blogsPostPhoto($blog_id, $photo_id, $title, $description, $blog_password='') {
        $params = array('auth_token'  => $this->token,
                        'blog_id'     => $blog_id,
                        'photo_id'    => $photo_id,
                        'title'       => $title,
                        'description' => $description);
        if($blog_password) $params['blog_password'] = $blog_password;
        $ret = $this->callMethod('flickr.blogs.postPhoto',$params);
        return $ret;    
    }

// Favorites

    function favoritesAdd($photo_id) {
        $params = array('auth_token'  => $this->token,
                        'photo_id'    => $photo_id);
        $ret = $this->callMethod('flickr.favorites.add',$params);
        return $ret;
    }

    function favoritesRemove($photo_id) {
        $params = array('auth_token'  => $this->token,
                        'photo_id'    => $photo_id);
        $ret = $this->callMethod('flickr.favorites.remove',$params);
        return $ret;
    }

    function favoritesGetList($user_id='',$extras='',$per_page='',$page='') {
        $params = array('auth_token'  => $this->token);
        if($user_id)  $params['user_id']  = $user_id;
        if($extras)   $params['extras']   = $extras;
        if($per_page) $params['per_page'] = $per_page;
        if($page)     $params['page']     = $page;
        $xml = $this->callMethod('flickr.favorites.getList',$params);
        if(!$xml) { return FALSE; }

        $i = 0;
        foreach($xml->photos->photo as $photo) {
            foreach($photo->attributes() as $k=>$v) {
                $ret[(string)$photo['id']][$k] = (string)$v;
            }
            $i++;
        }
        return $ret;
    }

    function favoritesGetPublicList($user_id='',$extras='',$per_page='',$page='') {
        $params = array();
        if($user_id)  $params['user_id']  = $user_id;
        if($extras)   $params['extras']   = $extras;
        if($per_page) $params['per_page'] = $per_page;
        if($page)     $params['page']     = $page;
        $xml = $this->callMethod('flickr.favorites.getPublicList',$params);
        if(!$xml) { return FALSE; }

        $i = 0;
        foreach($xml->photos->photo as $photo) {
            foreach($photo->attributes() as $k=>$v) {
                $ret[(string)$photo['id']][$k] = (string)$v;
            }
            $i++;
        }
        return $ret;
    }

// People

    function peopleFindByUsername($username) {
        $params = array('username'=>$username);
        $xml = $this->callMethod('flickr.people.findByUsername',$params);
        if(!$xml) { return FALSE; }

        $attr = $xml->user->attributes();
        return (string)$attr['nsid'];
    }

    function peopleGetInfo($nsid) {
        $params = array('user_id'=>$nsid);
        $xml = $this->callMethod('flickr.people.getInfo',$params);
        if(!$xml) { return FALSE; }

        foreach($xml->person->attributes() as $k=>$v) {
            $ret[$k] = (string)$v;
        }
        foreach($xml->person->children() as $k=>$v) {
            if($k=='photos') {
                foreach($v as $kk=>$vv) {
                    $ret['photos'][$kk] = (string)$vv;
                }
            } else $ret[$k] = (string)$v;
        }
        return $ret;
    }

    function peopleGetPublicPhotos($nsid,$extras='',$per_page='',$page='') {
        $params = array('user_id'=>$nsid);
        if($extras)   $params['extras']   = $extras;
        if($per_page) $params['per_page'] = $per_page;
        if($page)     $params['page']     = $page;
        $xml = $this->callMethod('flickr.people.getPublicPhotos',$params);
        if(!$xml) { return FALSE; }

        foreach($xml->photos->attributes() as $k=>$v) {
            $ret[$k] = (string)$v;
        }
        $i=0;
        foreach($xml->photos->photo as $photo) {
            foreach($photo->attributes() as $k=>$v) {
                $ret['photos'][(string)$photo['id']][$k] = (string)$v;
            }
            $i++;
        }
        return $ret;
    }

    function peopleFindByEmail($email, &$username='') {
        $params = array('find_email'=>$email);
        $xml = $this->callMethod('flickr.people.findByEmail',$params);
        if(!$xml) { return FALSE; }
        $attr = $xml->user->attributes();
        if(func_num_args()>1) $username = (string)$xml->user->username;
        return (string)$attr['nsid'];
    }

    function urlsLookupUser($url, &$username='') {
        $params = array('url'=>$url);
        $xml = $this->callMethod('flickr.urls.lookupUser',$params);
        if(!$xml) { return FALSE; }
        $attr = $xml->user->attributes();
        if(func_num_args()>1) $username = (string)$xml->user->username;
        return (string)$attr['id'];
    }

// Sets

    // Indices of the photoset arrays are set to the individual photoset ids.
    function photosetsGetList($nsid='') {
        $params = array();
        if($this->token) $params['auth_token'] = $this->token;
        if($nsid) $params['user_id']=$nsid;
        $xml = $this->callMethod('flickr.photosets.getList',$params);
        if(!$xml) { return FALSE; }
        foreach($xml->photosets->attributes() as $k=>$v) $ret[$k] = (string)$v;
        $i=0;
        foreach($xml->photosets->photoset as $k=>$v) {
            foreach($v->attributes() as $kk=>$vv) $ret['photosets'][(string)$v['id']][$kk] = (string)$vv;
            foreach($v->children() as $kk=>$vv) $ret['photosets'][(string)$v['id']][$kk] = (string)$vv;
            $i++;
        }
        return $ret;
    }

    function photosetsGetInfo($photoset_id) {
        $params = array('photoset_id' => $photoset_id);
        $xml = $this->callMethod('flickr.photosets.getInfo',$params);
        if(!$xml) { return FALSE; }
        foreach($xml->photoset->attributes() as $k=>$v) $ret[$k] = (string)$v;
        foreach($xml->photoset as $key=>$val) {
            $ret[$key] = $val;
        }
        return $ret;
    }

    function photosetsCreate($title, $primary_photo_id, $description='') {
        $params = array('auth_token'  => $this->token);
        $params['title'] = $title;
        $params['primary_photo_id'] = $primary_photo_id;
        if($description) $params['description'] = $description;
    
        $xml = $this->callMethod('flickr.photosets.create',$params);
        if(!$xml) { return FALSE; }
    
        foreach($xml->photoset->attributes() as $k=>$v) {
            $ret[$k] = (string)$v;
        }
        return $ret;
    }

    function photosetsDelete($photoset_id) {
        $params = array('auth_token'  => $this->token);
        $params['photoset_id'] = $photoset_id;

        $xml = $this->callMethod('flickr.photosets.delete',$params);
        if(!$xml) { return FALSE; }
        return TRUE;
    }

    function photosetsGetPhotos($photoset_id) {
        $params = array('photoset_id' => $photoset_id);
        $xml = $this->callMethod('flickr.photosets.getPhotos',$params);
        if(!$xml) { return FALSE; }
        foreach($xml->photoset->attributes() as $k=>$v) {
            $ret[$k] = (string)$v;
        }
        $i=0;
        foreach($xml->photoset->photo as $photo) {
            foreach($photo->attributes() as $k=>$v) {
                $ret['photos'][(string)$photo['id']][$k] = (string)$v;
            }
            $i++;
        }
        return $ret;
    }

    function photosetsAddPhoto($photoset_id, $photo_id) {
        $params = array('auth_token'  => $this->token);
        $params['photoset_id'] = $photoset_id;
        $params['photo_id'] = $photo_id;

        $xml = $this->callMethod('flickr.photosets.addPhoto',$params);
        if(!$xml) { return FALSE; }
        return TRUE;
    }

    function photosetsRemovePhoto($photoset_id, $photo_id) {
        $params = array('auth_token'  => $this->token);
        $params['photoset_id'] = $photoset_id;
        $params['photo_id'] = $photo_id;

        $xml = $this->callMethod('flickr.photosets.removePhoto',$params);
        if(!$xml) { return FALSE; }
        return TRUE;
    }

    function photosGetNotInSet($extras='',$perpage='',$page='') {
        $params = array('auth_token'  => $this->token);
        if($extras)   $params['extras']   = $extras;
        if($per_page) $params['perpage']  = $per_page;
        if($page)     $params['page']     = $page;
        $xml = $this->callMethod('flickr.photos.getNotInSet',$params);
        if(!$xml) { return FALSE; }

        foreach($xml->photos->attributes() as $k=>$v) {
            $ret[$k] = (string)$v;
        }
        $i=0;
        foreach($xml->photos->photo as $photo) {
            foreach($photo->attributes() as $k=>$v) {
                $ret['photos'][(string)$photo['id']][$k] = (string)$v;
            }
            $i++;
        }
        return $ret;
    }

    function photosGetRecent($extras='',$per_page='',$page='') {
        $params = array();
        if($extras)   $params['extras']   = $extras;
        if($per_page) $params['per_page'] = $per_page;
        if($page)     $params['page']     = $page;
        $xml = $this->callMethod('flickr.photos.getRecent',$params);
        if(!$xml) { return FALSE; }

        foreach($xml->photos->attributes() as $k=>$v) {
            $ret[$k] = (string)$v;
        }
        $i=0;
        foreach($xml->photos->photo as $photo) {
            foreach($photo->attributes() as $k=>$v) {
                $ret['photos'][(string)$photo['id']][$k] = (string)$v;
            }
            $i++;
        }
        return $ret;
    }

    function photosGetInfo($photo_id, $secret='') {
        $params = array();
        if($this->token) $params['auth_token'] = $this->token;
        $params['photo_id'] = $photo_id;
        if($secret) $params['secret'] = $secret;
        
        $xml = $this->callMethod('flickr.photos.getInfo',$params);
        if(!$xml) { return FALSE; }

        foreach($xml->photo->attributes() as $k=>$v) {
            $ret[(string)$k] = (string)$v;
        }
        foreach($xml->photo->children() as $k=>$v) {
            foreach($v->attributes() as $kk=>$vv) $ret[(string)$k][(string)$kk] = (string)$vv;
            $id = -1;
            foreach($v->children() as $kk=>$vv) {
                $typed = false;
                if(isset($vv['id'])) $id = (string)$vv['id'];
                else if(isset($vv['type'])) { $id = (string)$vv['type']; $typed = true; }
                else $id++;
                foreach($vv->attributes() as $kkk=>$vvv) $ret[(string)$k][$id][(string)$kkk] = (string)$vvv;
                if($typed) $ret[(string)$k][$id] = (string)$vv;
                else $ret[(string)$k][$id]['text'] = (string)$vv;
            }
            if(!count($ret[(string)$k])) $ret[(string)$k] = (string)$v;
        }
        return $ret;
    }

    function upload($photo, $title='', $description='', $tags='', $perms='', $async=1, &$info=NULL) {
        $tmpf = false;
        $params = array('auth_token'  => $this->token);
        $url = parse_url($photo);
        if(isset($url['scheme'])) {
            $stream = fopen($photo,'r');
            $tmpf = tempnam('/var/tmp','G2F');
            file_put_contents($tmpf, $stream);
            fclose($stream);
            $params['photo'] = $tmpf;    
        } else $params['photo'] = $photo;
        $info = filesize($params['photo']);
        if($title)       $params['title']       = $title;
        if($description) $params['description'] = $description;
        if($tags)        $params['tags']        = $tags;  // Space-separated string
        if($perms) {
            if(isset($perms['is_public'])) $params['is_public'] = $perms['is_public'];
            if(isset($perms['is_friend'])) $params['is_friend'] = $perms['is_friend'];
            if(isset($perms['is_family'])) $params['is_family'] = $perms['is_family'];
        }
        if($async)       $params['async']       = $async;
        $xml = $this->callMethod('upload',$params);
        if($tmpf) unlink($tmpf);
        if(!$xml) { return FALSE; }

        if($async) return((string)$xml->ticketid);
        else return((string)$xml->photoid);
    }

    function photosUploadCheckTickets($tickets) {
        if(is_array($tickets)) {
            foreach($tickets as $k=>$v) {
                if($k) $params['tickets'] .= ' ';
                $params['tickets'] .= $v;
            }
        } else {
            $params['tickets'] = $tickets;
        }
        $xml = $this->callMethod('flickr.photos.upload.checkTickets',$params);
        if(!$xml) { return FALSE; }
    
        foreach($xml->uploader->ticket as $ticket) {
            foreach($ticket->attributes() as $k=>$v) {
                $ret[(string)$ticket['id']][$k] = (string)$v;
            }
        }
        return $ret;
    }

// Notes

    function photosNotesAdd($photo_id,$x,$y,$w,$h,$text) {
        $params = array('auth_token'  => $this->token);
        $params['photo_id'] = $photo_id;
        $params['note_x'] = $x;    
        $params['note_y'] = $y;    
        $params['note_w'] = $w;    
        $params['note_h'] = $h;    
        $params['note_text'] = $text;    

        $xml = $this->callMethod('flickr.photos.notes.add',$params);
        if(!$xml) { return FALSE; }

        return $xml->note['id'];
    }

    function photosNotesEdit($node_id,$x,$y,$w,$h,$text) {
        $params = array('auth_token'  => $this->token);
        $params['note_id'] = $note_id;
        $params['note_x'] = $x;    
        $params['note_y'] = $y;    
        $params['note_w'] = $w;    
        $params['note_h'] = $h;    
        $params['note_text'] = $text;    

        $xml = $this->callMethod('flickr.photos.notes.edit',$params);
        if(!$xml) { return FALSE; }

        return $xml->note['id'];
    }

    function photosNotesDelete($note_id) {
        $params = array('auth_token'  => $this->token);
        $params['note_id'] = $node_id;    

        $xml = $this->callMethod('flickr.photos.notes.delete',$params);
        if(!$xml) { return FALSE; }

        return TRUE;
    }

    function photosSearch($user_id='',$tags='',$tag_mode='',$text='',$min_upload_date='',
                          $max_upload_date='',$min_taken_date='',$max_taken_date='',
                          $license='',$extras='',$per_page='',$page='',$sort='') {
        if(is_array($user_id)) $params = $user_id;
        else { 
            $params = array();    
            if($user_id)  $params['user_id']  = $user_id;
            if($tags)     $params['tags']     = $tags;
            if($tag_mode) $params['tag_mode'] = $tag_mode;
            if($text)     $params['text']     = $text;
            if($min_upload_date) $params['min_upload_date'] = $min_upload_date;
            if($max_upload_date) $params['max_upload_date'] = $max_upload_date;
            if($min_taken_date)  $params['min_taken_date']  = $min_taken_date;
            if($max_taken_date)  $params['max_taken_date']  = $max_taken_date;
            if($license)  $params['license']  = $license;
            if($extras)   $params['extras']   = $extras;
            if($per_page) $params['per_page'] = $per_page;
            if($page)     $params['page']     = $page;
            if($sort)     $params['sort']     = $sort;
        }

        $xml = $this->callMethod('flickr.photos.search',$params);
        if(!$xml) { return FALSE; }

        foreach($xml->photos->attributes() as $k=>$v) {
            $ret[$k] = (string)$v;
        }
        $i=0;
        foreach($xml->photos->photo as $photo) {
            foreach($photo->attributes() as $k=>$v) {
                $ret['photos'][(string)$photo['id']][$k] = (string)$v;
            }
            $i++;
        }
        return $ret;
    }

// Reflection

    function reflectionGetMethods() {
        $params = array();
        $xml = $this->callMethod('flickr.reflection.getMethods',$params);
        if(!$xml) { return FALSE; }

        $ret = (array)$xml->methods->method;
        return $ret;
    }

}
/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: sw=4 ts=4 fdm=marker
 * vim<600: sw=4 ts=4
 */
?>

