<?php
$link = new mysqli("localhost", "root", "cheese12345", "forum");

class board{

    function list_forums(){
        $query = "SELECT group_concat(id) FROM forums";
        $result = do_query($query);
        $result = explode(",",$result->fetch_array()[0]);
        return $result;   
    }
}

class forum{
    public $id;
    public $name;
    function get_forum($id){
        $this->id = escape($id);
        $query = "SELECT name FROM forums WHERE id='$this->id'";
        $result = do_query($query);
        $this->name = $result->fetch_array()[0];
    }
    function create_forum($name){
        $this->name = escape($name);
        $query = "INSERT INTO forums (name) VALUES ('$this->name')";
        do_query($query);
        $this->id = get_last_id();
    }
    function list_threads(){
        $query = "SELECT group_concat(id) FROM threads WHERE forumid='$this->id'";
        $result = do_query($query);
        $result = explode(",",$result->fetch_array()[0]);
        return $result;   
    }
}

class thread{
    public $id;
    public $forumid;
    public $threadname;
    public $userid;

    function get_thread($id){
        $this->id = escape($id);
        $query = "SELECT forumid,threadname,userid FROM threads WHERE id='$this->id'";
        $result = do_query($query)->fetch_array();

        $this->forumid = $result[0];
        $this->threadname = $result[1];
        $this->userid = $result[2];
    }

    function new_thread($forumid, $threadname, $userid, $content){
        $this->forumid = escape($forumid);
        $this->threadname = escape($threadname);
        $this->userid = escape($userid);

        $query = "INSERT INTO threads (forumid,threadname,userid) VALUES ('$this->forumid','$this->threadname','$this->userid')";

        do_query($query);

        $this->id = get_last_id();

        $post = new post();
        $post->new_post($this->userid,$this->id,$content);
    }

    function reply($userid, $content){
        $post = new post();
        $post->new_post($userid,$this->id,$content);
    }
    function list_posts(){
        $query = "SELECT group_concat(id) FROM posts WHERE threadid = '$this->id'";

        $result = do_query($query);
        $result = explode(",",$result->fetch_array()[0]);
        return $result;   
    }
}

class post{
    public $id;
    public $userid;
    public $threadid;
    public $date;
    public $content;

    function get_post($id){

        $this->id = escape($id);

        $query = "SELECT userid,threadid,date,content FROM posts WHERE id='$this->id'";
        $result = do_query($query)->fetch_array();


        $this->userid = $result[0];
        $this->threadid = $result[1];
        $this->date = $result[2];
        $this->content = $result[3];
    }

    function new_post($userid, $threadid, $content){
        $this->date = get_date();
        $this->userid = escape($userid);
        $this->threadid = escape($threadid);
        $this->content = nl2br(escape($content));

        $query = "INSERT INTO posts (userid,threadid,date,content) VALUES ('$this->userid','$this->threadid','$this->date','$this->content')";

        do_query($query);
    }

} 


function do_query($query){
    $result = $GLOBALS['link']->query($query) or die($GLOBALS['link']->error);

    return $result;
}

function escape($string){
    return htmlentities($GLOBALS['link']->real_escape_string($string));
}

function get_last_id(){
    return $GLOBALS['link']->insert_id;
}

function get_date(){
    return date("H:i:s d/m/Y");
}

$post = new post();
$post->get_post(1);

var_dump($post);
?>
