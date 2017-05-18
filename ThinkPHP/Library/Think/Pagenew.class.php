<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// |         lanfengye <zibin_5257@163.com>
// +----------------------------------------------------------------------
namespace Think;

class Pagenew {
    
    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 分页URL地址
    public $url     =   '';
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow    ;
    // 分页总页面数
    public $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
    protected $config  =    array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
    // 默认分页变量名
    protected $varPage;

    /**
     * 架构函数
     * @access public
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows,$listRows='',$parameter='',$url='') {
        $this->totalRows    =   $totalRows;
        $this->parameter    =   $parameter;
        $this->varPage      =   C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        if(!empty($listRows)) {
            $this->listRows =   intval($listRows);
        }
        $this->totalPages   =   ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages    =   ceil($this->totalPages/$this->rollPage);
        $this->nowPage      =   !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
        if($this->nowPage<1){
            $this->nowPage  =   1;
        }elseif(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage  =   $this->totalPages;
        }
        $this->firstRow     =   $this->listRows*($this->nowPage-1);
        if(!empty($url))    $this->url  =   $url; 
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     * 分页显示输出  样式一
     * @access public
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p              =   $this->varPage;
        $nowCoolPage    =   ceil($this->nowPage/$this->rollPage);

        // 分析分页参数
        if($this->url){
            $depr       =   C('URL_PATHINFO_DEPR');
            $url        =   rtrim(U('/'.$this->url,'',false),$depr).$depr.'__PAGE__';
        }else{
            if($this->parameter && is_string($this->parameter)) {
                parse_str($this->parameter,$parameter);
            }elseif(is_array($this->parameter)){
                $parameter      =   $this->parameter;
            }elseif(empty($this->parameter)){
                unset($_GET[C('VAR_URL_PARAMS')]);
                $var =  !empty($_POST)?$_POST:$_GET;
                if(empty($var)) {
                    $parameter  =   array();
                }else{
                    $parameter  =   $var;
                }
            }
            $parameter[$p]  =   '__PAGE__';
            $url            =   U('',$parameter);
        }
        //上下翻页字符串
        $upRow          =   $this->nowPage-1;
        $downRow        =   $this->nowPage+1;
        if ($upRow>0){
            $upPage     =   "<a href='".str_replace('__PAGE__',$upRow,$url)."'>".$this->config['prev']."</a>";
        }else{
            $upPage     =   '';
        }

        if ($downRow <= $this->totalPages){
            $downPage   =   "<a href='".str_replace('__PAGE__',$downRow,$url)."'>".$this->config['next']."</a>";
        }else{
            $downPage   =   '';
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst   =   '';
            $prePage    =   '';
        }else{
            $preRow     =   $this->nowPage-$this->rollPage;
            $prePage    =   "<a href='".str_replace('__PAGE__',$preRow,$url)."' >上".$this->rollPage."页</a>";
            $theFirst   =   "<a href='".str_replace('__PAGE__',1,$url)."' >".$this->config['first']."</a>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage   =   '';
            $theEnd     =   '';
        }else{
            $nextRow    =   $this->nowPage+$this->rollPage;
            $theEndRow  =   $this->totalPages;
            $nextPage   =   "<a href='".str_replace('__PAGE__',$nextRow,$url)."' >下".$this->rollPage."页</a>";
            $theEnd     =   "<a href='".str_replace('__PAGE__',$theEndRow,$url)."' >".$this->config['last']."</a>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page       =   ($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    $linkPage .= "<a href='".str_replace('__PAGE__',$page,$url)."'>".$page."</a>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    $linkPage .= "<span class='current'>".$page."</span>";
                }
            }
        }
        $pageStr     =   str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->config['theme']);
        return $pageStr;
    }
    
    /*
    * 分页样式二
    */
     public function showtwo() {
        $multipage = ''; 
        if($this->totalRows <= $this->listRows) { 
            return  $multipage;
        }
        if(0 == $this->totalRows) return '';
        $p              =   $this->varPage;
        $nowCoolPage    =   ceil($this->nowPage/$this->rollPage);

        // 分析分页参数
        if($this->url){
            $depr       =   C('URL_PATHINFO_DEPR');
            $url        =   rtrim(U('/'.$this->url,'',false),$depr).$depr.'__PAGE__';
        }else{
            if($this->parameter && is_string($this->parameter)) {
                parse_str($this->parameter,$parameter);
            }elseif(is_array($this->parameter)){
                $parameter      =   $this->parameter;
            }elseif(empty($this->parameter)){
                unset($_GET[C('VAR_URL_PARAMS')]);
                $var =  !empty($_POST)?$_POST:$_GET;
                if(empty($var)) {
                    $parameter  =   array();
                }else{
                    $parameter  =   $var;
                }
            }
            $parameter[$p]  =   '__PAGE__';
            $url            =   U('',$parameter);
        }
        
        $pages = $this->totalPages;  // 总页数 
        $curr_page = $this->nowPage; //当前页数
        $page = 6; //默认显示的页数
        $offset = 3;  //左移
        $from = $curr_page - $offset; 
        $to = $curr_page + $page - $offset - 1; 
        if($page > $pages){ 
            $from = 1; 
            $to = $pages; 
        }else{ 
            if($from < 1){ 
                $to = $curr_page + 1 - $from; 
                $from = 1; 
                if(($to - $from) < $page && ($to - $from) < $pages){ 
                    $to = $page; 
                } 
            }elseif($to > $pages){ 
                $from = $curr_page - $pages + $to; 
                $to = $pages; 
                if(($to - $from) < $page && ($to - $from) < $pages){ 
                    $from = $pages - $page + 1; 
                } 
            } 
        }
       
        $uppage=$curr_page-1;
       if($curr_page !=1){
            $multipage .= "<a href='".str_replace('__PAGE__',$uppage,$url)."'>&laquo;上一页</a>"; 
       }else{
            $multipage .= "<a class='active'>&laquo;上一页</a>";
       }

        if($from > 2){
          $multipage .= "<a href='".str_replace('__PAGE__',1,$url)."'>1</a>";
          $multipage .= "<span>....</span>";
        }elseif($from == 2){
            $multipage .= "<a href='".str_replace('__PAGE__',1,$url)."'>1</a>";
        }
        
        for($i = $from; $i <= $to; $i++) {
            if($i != $curr_page){ 
                $multipage .= "<a href='".str_replace('__PAGE__',$i,$url)."'>$i</a>"; 
            }else{ 
                $multipage .= "<a class='active'>$i</a>"; 
            } 
        }
        
        if($to+1 < $pages){
          $multipage .= "<span>....</span>";
          $multipage .= "<a href='".str_replace('__PAGE__',$pages,$url)."'>$pages</a>";
        }elseif($to+1 == $pages){
          $multipage .= "<a href='".str_replace('__PAGE__',$pages,$url)."'>$pages</a>";
        }   
        
        $nextpage=$curr_page+1;
        if($nextpage <= $pages){
            $multipage .="<a href='".str_replace('__PAGE__',$nextpage,$url)."'>下一页&raquo;</a>";
         }else{
            $multipage .= "<a class='active'>下一页&raquo;</a>";
         }
        //$urlStr= $url;//$this->urlhandel(__SELF__);
        $multipage.="到第<input style='width: 30px;' type='text' id='pagenum'/>页<a href='javascript:void(0)' onclick='goPagenum()'>确定</a>";
        $multipage.="
        <script>
                    function goPagenum(){
                        var pagenum=$('#pagenum').val();
                        var urlstr = '{$url}';
                        var url=urlstr.replace('__PAGE__',pagenum);
                        window.location.href=url;
                    }
        </script>
        ";
        return $multipage;
    }
    
    /*
    * 分页样式三
    */
     public function showthr() {
        $multipage = ''; 
        if($this->totalRows <= $this->listRows) { 
            return  $multipage;
        }
        if(0 == $this->totalRows) return '';
        $p              =   $this->varPage;
        $nowCoolPage    =   ceil($this->nowPage/$this->rollPage);

        // 分析分页参数
        if($this->url){
            $depr       =   C('URL_PATHINFO_DEPR');
            $url        =   rtrim(U('/'.$this->url,'',false),$depr).$depr.'__PAGE__';
        }else{
            if($this->parameter && is_string($this->parameter)) {
                parse_str($this->parameter,$parameter);
            }elseif(is_array($this->parameter)){
                $parameter      =   $this->parameter;
            }elseif(empty($this->parameter)){
                unset($_GET[C('VAR_URL_PARAMS')]);
                $var =  !empty($_POST)?$_POST:$_GET;
                if(empty($var)) {
                    $parameter  =   array();
                }else{
                    $parameter  =   $var;
                }
            }
            $parameter[$p]  =   '__PAGE__';
            $url            =   U('',$parameter);
        }
        
        $pages = $this->totalPages;  // 总页数 
        $curr_page = $this->nowPage; //当前页数
        $page = 6; //默认显示的页数
        $offset = 3;  //左移
        $from = $curr_page - $offset; 
        $to = $curr_page + $page - $offset - 1; 
        if($page > $pages){ 
            $from = 1; 
            $to = $pages; 
        }else{ 
            if($from < 1){ 
                $to = $curr_page + 1 - $from; 
                $from = 1; 
                if(($to - $from) < $page && ($to - $from) < $pages){ 
                    $to = $page; 
                } 
            }elseif($to > $pages){ 
                $from = $curr_page - $pages + $to; 
                $to = $pages; 
                if(($to - $from) < $page && ($to - $from) < $pages){ 
                    $from = $pages - $page + 1; 
                } 
            } 
        }
       
        $uppage=$curr_page-1;
       if($curr_page !=1){
            $multipage .= "<a href='javascript:void(0)' onclick='goPagenum(".$uppage.")'>&laquo;上一页</a>"; 
       }else{
            $multipage .= "<a class='active'>&laquo;上一页</a>";
       }

        if($from > 2){
          $multipage .= "<a href='javascript:void(0)' onclick='goPagenum(1)'>1</a>";
          $multipage .= "<span>....</span>";
        }elseif($from == 2){
            $multipage .= "<a href='javascript:void(0)' onclick='goPagenum(1)'>1</a>";
        }
        
        for($i = $from; $i <= $to; $i++) {
            if($i != $curr_page){ 
                $multipage .= "<a href='javascript:void(0)' onclick='goPagenum(".$i.")'>$i</a>"; 
            }else{ 
                $multipage .= "<a class='active'>$i</a>"; 
            } 
        }
        
        if($to+1 < $pages){
          $multipage .= "<span>....</span>";
          $multipage .= "<a href='javascript:void(0)' onclick='goPagenum(".$pages.")'>$pages</a>";
        }elseif($to+1 == $pages){
          $multipage .= "<a href='javascript:void(0)' onclick='goPagenum(".$pages.")'>$pages</a>";
        }   
        
        $nextpage=$curr_page+1;
        if($nextpage <= $pages){
            $multipage .="<a href='javascript:void(0)' onclick='goPagenum(".$nextpage.")'>下一页&raquo;</a>";
         }else{
            $multipage .= "<a class='active'>下一页&raquo;</a>";
         }
        //$urlStr= $url;//$this->urlhandel(__SELF__);
        $multipage.="到第<input style='width: 30px;' type='text' id='pagenum'/>页<a href='javascript:void(0)' onclick='goPagenum(0)'>确定</a>";
        $multipage.="
        <script>
                    function goPagenum(temp_nowpage){
                       if(temp_nowpage==0){
                            var pagenum=$('#pagenum').val();
                        }else{
                            var pagenum=temp_nowpage;
                        }
                        var urlstr = '{$url}';
                        var url=urlstr.replace('__PAGE__',pagenum);
                        bloglist(url);
                    }
        </script>
        ";
        return $multipage;
    }

    /*
    * 广场任务分页
    */
    public function showcpcs() {
        $multipage = '';
        if($this->totalRows <= $this->listRows) {
            return  $multipage;
        }
        if(0 == $this->totalRows) return '';
        $p              =   $this->varPage;
        $nowCoolPage    =   ceil($this->nowPage/$this->rollPage);

        // 分析分页参数
        if($this->url){
            $depr       =   C('URL_PATHINFO_DEPR');
            $url        =   rtrim(U('/'.$this->url,'',false),$depr).$depr.'__PAGE__';
        }else{
            if($this->parameter && is_string($this->parameter)) {
                parse_str($this->parameter,$parameter);
            }elseif(is_array($this->parameter)){
                $parameter      =   $this->parameter;
            }elseif(empty($this->parameter)){
                unset($_GET[C('VAR_URL_PARAMS')]);
                $var =  !empty($_POST)?$_POST:$_GET;
                if(empty($var)) {
                    $parameter  =   array();
                }else{
                    $parameter  =   $var;
                }
            }
            $parameter[$p]  =   '__PAGE__';
            $url            =   U('',$parameter);
        }

        $pages = $this->totalPages;  // 总页数
        $curr_page = $this->nowPage; //当前页数
        $page = 6; //默认显示的页数
        $offset = 3;  //左移
        $from = $curr_page - $offset;
        $to = $curr_page + $page - $offset - 1;
        if($page > $pages){
            $from = 1;
            $to = $pages;
        }else{
            if($from < 1){
                $to = $curr_page + 1 - $from;
                $from = 1;
                if(($to - $from) < $page && ($to - $from) < $pages){
                    $to = $page;
                }
            }elseif($to > $pages){
                $from = $curr_page - $pages + $to;
                $to = $pages;
                if(($to - $from) < $page && ($to - $from) < $pages){
                    $from = $pages - $page + 1;
                }
            }
        }

        $uppage=$curr_page-1;
        if($curr_page !=1){
            $multipage .= "<a href='".str_replace('__PAGE__',$uppage,$url)."'>&laquo;上一页</a>";
        }else{
            $multipage .= "<a class='active'>&laquo;上一页</a>";
        }

        if($from > 2){
            $multipage .= "<a href='".str_replace('__PAGE__',1,$url)."'>1</a>";
            $multipage .= "<span>....</span>";
        }elseif($from == 2){
            $multipage .= "<a href='".str_replace('__PAGE__',1,$url)."'>1</a>";
        }

        for($i = $from; $i <= $to; $i++) {
            if($i != $curr_page){
                $multipage .= "<a href='".str_replace('__PAGE__',$i,$url)."'>$i</a>";
            }else{
                $multipage .= "<a class='se'>$i</a>";
            }
        }

        if($to+1 < $pages){
            $multipage .= "<span>....</span>";
            $multipage .= "<a href='".str_replace('__PAGE__',$pages,$url)."'>$pages</a>";
        }elseif($to+1 == $pages){
            $multipage .= "<a href='".str_replace('__PAGE__',$pages,$url)."'>$pages</a>";
        }

        $nextpage=$curr_page+1;
        if($nextpage <= $pages){
            $multipage .="<a href='".str_replace('__PAGE__',$nextpage,$url)."'>下一页&raquo;</a>";
        }else{
            $multipage .= "<a class='active'>下一页&raquo;</a>";
        }
        return $multipage;
    }

    public function urlhandel($str){
        $infoarr=  explode("/p/", $str);
        return $infoarr[0];
    }
}