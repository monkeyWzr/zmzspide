<?php
include_once('regex.php');

/**
 * zimuzu.tv小爬虫
 */
class Spider{

    public $cookie_file_path;
    private $login_status = FALSE;
    public $itemid;
    public $itemname;
    public $itempage;
    // public $lb;

    function __construct($itemname=NULL){
        if($itemname){
            $this->itemname = $itemname;
            $this->itemid = $this->get_itemid($itemname);
            $this->itempage = $this->get_itempage($this->itemid);
        }
        else{
            $this->itemname = '';
            $this->itemid = '';
            $this->itempage = '';
        }
        $this->cookie_file_path = dirname(__FILE__).'/cookie.txt';
    }

    function set_cookie_path($cookie_file_path){
        $this->cookie_file_path = $cookie_file_path;
    }

    /**
     * 模拟登陆
     * 保存cookie
     * @param  string $username 用户名
     * @param  string $password 密码
     * @return bool
     */
    function login($username, $password){
        if(file_exists($this->cookie_file_path))
            unlink($this->cookie_file_path);
        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                         //CURLOPT_FOLLOWLOCATION => TRUE,
                         CURLOPT_COOKIEJAR => $this->cookie_file_path,
                         CURLOPT_POSTFIELDS => 'account=' . urlencode($username) . '&password=' . $password . '&remember=1&url_back=http%3A%2F%2Fwww.zimuzu.tv%2F'
                    );

        $login = curl_init("http://www.zimuzu.tv/User/Login/ajaxLogin");
        curl_setopt_array($login, $options);

        // $json_rtn格式示例：
        /**
        object(stdClass)[1]
            public 'status' => int 1
            public 'info' => string '登录成功！' (length=15)
            public 'data' =>
            object(stdClass)[2]
                public 'url_back' => string 'http://www.zimuzu.tv/' (length=21)
        */
        //json_decode第二个参数设为TRUE则返回数组格式
        $json_rtn = json_decode(curl_exec($login), FALSE);
        curl_close($login);
        $this->login_status = $json_rtn->status;
        echo $json_rtn->info;
        return $json_rtn->status;
    }

    /**
     * 获取剧集id
     * @param  string $itemname 剧集名称
     * @return string
     */
    function get_itemid($itemname){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.zimuzu.tv/search?keyword={$itemname}");
        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                         CURLOPT_HEADER => FALSE
                    );
        curl_setopt_array($ch, $options);
        $searchpage = curl_exec($ch);
        curl_close($ch);
        $regex = '|<div\sclass="clearfix\ssearch-item">.*电视剧.*<a\shref="/resource/(.*)"><img\ssrc=".*"></a><\/div>|sU';
        $match = array();
        preg_match($regex, $searchpage, $match);
        if(!empty($match)){
            return $match[1];
        }
        else{
            return FALSE;
        }
    }

    /**
     * 获取剧集名称
     * @param  string $itemid 剧集id
     * @return string/FALSE
     */
    function get_itemname($itemid){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.zimuzu.tv/resource/{$itemid}");
        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                         CURLOPT_HEADER => FALSE,
                         CURLOPT_FOLLOWLOCATION => TRUE
                         // CURLOPT_COOKIEJAR => $this->cookie_file_path,
                         // CURLOPT_POSTFIELDS => 'account=' . urlencode($username) . '&password=' . $password . '&remember=1&url_back=http%3A%2F%2Fwww.zimuzu.tv%2F'
                    );
        curl_setopt_array($ch, $options);
        $searchpage = curl_exec($ch);
        curl_close($ch);
        // echo $searchpage;
        // var_dump($searchpage);
        //这里改一下
        $regex = '|<h2\sclass="resource-tit">【.*】(.*)<label\sid="play_status">(.*)<\/label><\/h2>|sU';
        // <h2 class="resource-tit">【美剧】《权力的游戏》<label id="play_status">第6季完结</label></h2>
        $match = array();
        preg_match($regex, $searchpage, $match);
        // var_dump($match);
        if(!empty($match)){
            return $match[1];
        }
        else{
            return FALSE;
        }
    }

    /**
     * 获取状态
     * @param  string $itemid 剧集id
     * @return 未完成
     */
    function get_status($itemid){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.zimuzu.tv/resource/index_json/rid/{$itemid}/channel/tv");
        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                         CURLOPT_HEADER => TRUE,
                         // CURLOPT_FOLLOWLOCATION => TRUE
                         // CURLOPT_COOKIEJAR => $this->cookie_file_path,
                         // CURLOPT_POSTFIELDS => 'account=' . urlencode($username) . '&password=' . $password . '&remember=1&url_back=http%3A%2F%2Fwww.zimuzu.tv%2F'
                    );
        curl_setopt_array($ch, $options);
        $searchpage = curl_exec($ch);
        curl_close($ch);
        // readfile("http://www.zimuzu.tv/resource/index_json/rid/{$itemid}/channel/tv");/
        // echo $searchpage;
        var_dump($searchpage);
        // echo file_get_contents("http://www.zimuzu.tv/resource/index_json/rid/{$itemid}/channel/tv");
        //
    }

    /**
     * 获取剧集页面
     * @param  string $itemid 剧集id
     * @return string
     */
    function get_itempage($itemid){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.zimuzu.tv/resource/{$itemid}");
        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                         CURLOPT_HEADER => FALSE,
                         CURLOPT_FOLLOWLOCATION => TRUE
                         // CURLOPT_COOKIEJAR => $this->cookie_file_path,
                         // CURLOPT_POSTFIELDS => 'account=' . urlencode($username) . '&password=' . $password . '&remember=1&url_back=http%3A%2F%2Fwww.zimuzu.tv%2F'
                    );
        curl_setopt_array($ch, $options);
        $itempage = curl_exec($ch);
        // curl_close($ch);
        // var_dump($match);
        curl_close($ch);
        return $itempage;
    }

    /**
     * 获取基本信息
     * @return array
     */
    function get_baseinfo(){
        $match = array();
        $regex = '|<div\sclass="fl-info">.*<ul>.*<li><strong><span>.*<\/span>(.*)<\/strong>.*<strong\sclass="even"><span>.*<\/span>(.*)<\/strong><\/li>.*<li><strong><span>.*<\/span>(.*)<\/strong>.*<strong\sclass="even"><span>.*<\/span>(.*)<\/strong><\/li>.*<li><strong><span>.*<\/span>(.*)<\/strong>.*<strong\sclass="even"><span>.*<\/span>(.*)<\/strong><\/li>.*<li><strong><span>.*<\/span>(.*)<\/strong>.*<div\sclass="clearfix.*|sU';
        preg_match($regex, $this->itempage, $match);
        unset($match[0]);
        return array_values($match);
        // array_pop($match);
        // $intro = strip_tags($match[1]);
    }

    /**
     * 获取简介
     * @return string
     */
    function get_intro(){
        $match = array();
        $regex = '|<div\sclass="fl-info">.*<a\shref="javascript:void\(0\)"\sclass="f2">\[展开全文\]<\/a>.*<div\sstyle="display:none;">(.*)<\/div>.*<div\sclass="clearfix.*|sU';
        preg_match($regex, $this->itempage, $match);
        $intro = strip_tags($match[1]);
        return $intro;
    }

    /**
     * 获取编剧
     * @param  string $itempage 剧集主页
     * @return array           编剧
     */
    function get_screenwriter(){
        $match = array();
        $regex = '|<div\sclass="fl-info">.*<li><span>編劇：<\/span>(<a.*>.*<\/a>)<\/li>.*<\/div>.*<div\sclass="clearfix.*|sU';
        preg_match($regex, $this->itempage, $match);
        if(empty($match)){
            return FALSE;
        }
        else{
            $screenwriter = explode(' / ', strip_tags( $match[1]));
            return $screenwriter;
        }
    }

    /**
     * 获取导演
     * @param  string $itempage 剧集主页
     * @return array           导演
     */
    function get_director(){
        $match = array();
        $regex = '|<div\sclass="fl-info">.*<li><span>导演：<\/span>(<a.*>.*<\/a>)<\/li>.*<\/div>.*<div\sclass="clearfix.*|sU';
        preg_match($regex, $this->itempage, $match);
        if(empty($match)){
            return FALSE;
        }
        // var_dump($match);
        else{
            $director = explode(' / ', strip_tags( $match[1]));
            return $director;
        }
    }

    /**
     * 获取主演
     * @param  string $itempage 剧集主页
     * @return array           主演
     */
    function get_starring(){
        $match = array();
        $regex = '|<div\sclass="fl-info">.*<li.*><span>主演：<\/span>(<a.*>.*<\/a>)<\/li>.*<\/div>.*<div\sclass="clearfix.*|sU';
        preg_match($regex, $this->itempage, $match);
        if(empty($match)){
            return FALSE;
        }
        // var_dump($match);
        else{
            $starring = explode(' / ', strip_tags( $match[1]));
            return $starring;
        }
    }

    /**
     * 获取图片
     * @param  string $itempage 剧集主页
     * @return string           图片链接
     */
    function get_image(){
        $match = array();
        $regex = '|<div\sclass="fl-img">.*<p><a\shref="(.*)"\sclass="imglink"\starget="_blank">|sU';
        preg_match($regex, $this->itempage, $match);
        if(!empty($match)){
            return $match[1];
        }
        else{
            return FALSE;
        }
    }

    /**
     * 获取放送时间表
     * @return array
     */
    function get_schedule(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.zimuzu.tv/tv/eschedule");
        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                         CURLOPT_HEADER => FALSE,
                         CURLOPT_FOLLOWLOCATION => TRUE
                    );
        curl_setopt_array($ch, $options);
        $itempage = curl_exec($ch);
        // curl_close($ch);
        // var_dump($match);
        curl_close($ch);
        // return $itempage;
        $match = array();
        $regex = '|<td class="ihbg ">.*<dl>.*<dt>(.*)</dt>(.*</dd>).*</dl>|sU';
        preg_match($regex, $itempage, $match);
        if(!empty($match)){
            return $match;
        }
        else{
            return FALSE;
        }
    }

    /**
     * 获取单一剧集链接
     * @param  string $season    季数
     * @param  string $episode   集数
     * @param  string $link_type 下载链接类型，当前仅支持ed2k和magnet
     * @param  string $format    文件格式
     * @return array
     */
    function get_episode($season='', $episode='', $link_type='ed2k', $format='HR-HDTV'){
        if(!$this->login_status){
            echo '未登录！';
            return;
        }

        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_COOKIEFILE => $this->cookie_file_path
                    );
        $ch = curl_init('http://www.zimuzu.tv/resource/list/'.$this->itemid);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        // echo $result;
        $regex = '|<li\sclass="clearfix"\sformat="' . $format . '"\sseason="' . $season . '"\sepisode="' . $episode . '".*>.*<a\stitle=.*\sitemid="(.*)">(.*)<\/a>.*<font.*>(.*)<\/font>(.*)<\/li>|sU';
        $match = array();
        preg_match($regex, $result, $match);
         $link = array();
        if($link_type == 'ed2k'){
            preg_match(Regex::ED2K_LINK, $match[4], $link);
        }
        elseif($link_type == 'magnet'){
            preg_match(Regex::MAGNET_LINK, $match[4], $link);
        }
        $rtn = array();
        $rtn['episode_id'] = $match[1];
        $rtn['title'] = $match[2];
        $rtn['size'] = $match[3];
        $rtn['link'] = $link[1];
        $rtn['link_type'] = $format;

        return $rtn;
    }

    /**
     * 获取一季的下载链接
     * @param  string $season    季数
     * @param  string $link_type 链接类型
     * @param  string $format    文件格式
     * @return array
     */
    public function get_season($season='', $link_type='ed2k', $format='HR-HDTV'){
        if($this->login_status == FALSE){
            echo '  未登录状态，无法获取资源列表！';
            return;
        }
        $options = array(CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_COOKIEFILE => $this->cookie_file_path
                    );
        $ch = curl_init('http://www.zimuzu.tv/resource/list/'.$this->itemid);
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        $regex = '|<li\sclass="clearfix"\sformat="' . $format . '"\sseason="' . $season . '".*>.*<a\stitle=.*\sitemid="(.*)">(.*)<\/a>.*<font.*>(.*)<\/font>(.*)<\/li>|sU';
        $matches = array();
        preg_match_all($regex, $result, $matches);
        $links = array('season' => $season, 'links' => array());
        $type = '';
        if($link_type == 'ed2k'){
            $type = Regex::ED2K_LINK;
        }
        elseif($link_type == 'magnet'){
            $type = Regex::MAGNET_LINK;
        }

        foreach($matches[4] as $match){
            preg_match(Regex::ED2K_LINK, $match, $link);
            $links['links'][] = $link[1];
        }

        return $links;
    }
}
