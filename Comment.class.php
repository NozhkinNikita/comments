
<?php
class Comment
{
    private $tree=array();
    private $use = array();
    private $index = array();
    private $data = array();
    public function __construct()
    {

        $this->tree[0] = array();
    }
	//bulding hierarchical tree of comments
	//input: $id-child id, $parentId-$parent id
    private function addChild($id, $parentId)
    {
        if (!array_key_exists($parentId, $this->tree)) {
            $this->tree[$parentId] = array();
        }
        array_push($this->tree[$parentId], $id);
    }
	//connect to db
    private function dbConnect()
    {
        $link = mysqli_connect("localhost", "root", "", "comments");
        
        /* check connection */
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        
        return $link;
    }
   	//add comment to databse 
	//input 
	//$parentId =parent id of Comment	
	//$name- authors name
	//$text- text of comment
    public function addComment($parentId, $name, $text)
    {
        $link = $this->dbConnect();
        mysqli_query($link, "INSERT INTO comment (parentId,name,text,date) VALUES ('$parentId','$name','$text',NOW())");
        
        
    }
	//return last id of comment
    public function getLastId()
    {
        $link   = $this->dbConnect();
        $result = mysqli_query($link, "SELECT id from comment ORDER BY id DESC");
        
        
        while ($row = $result->fetch_assoc()) {
            return ($row['id']);
            
        }
        
    }
	//recursive supporting function for sorting comments in order to output
    private function printCom($tree, $level)
    {
        
        foreach ($tree as $key => $value) {
            
            if (is_array($value)) {
                if ($this->use[$key] == 1)
                    continue;
                $this->printCom($value, $level);
            } else {
                
                if (array_key_exists($value, $this->tree)) {
                    
                    $tmp          = array();
                    $tmp['id']    = $value;
                    $tmp['level'] = $level;
                    array_push($this->index, $tmp);
                    $this->use[$value] = 1;
                    $this->printCom($this->tree[$value], $level + 1);
                    
                } else {
                    $tmp          = array();
                    $tmp['id']    = $value;
                    $tmp['level'] = $level;
                    array_push($this->index, $tmp);
                }
                
            }
            
        }
        
    }
    //recursive supporting function for search all  comments id  for delete
    private function deleteCom($tree)
    {
        
        foreach ($tree as $key => $value) {
            
            //echo "Ключ: $key; Значение: $value<br />\n";
            if (is_array($value)) {
                if ($this->use[$key] == 1)
                    continue;
                $this->printCom($value, $level);
            } else {
                
                if (array_key_exists($value, $this->tree)) {
                    
                    
                    array_push($this->index, $value);
                    $this->use[$value] = 1;
                    $this->deleteCom($this->tree[$value]);
                    
                } else {
                    $tmp = array();
                    
                    array_push($this->index, $value);
                }
                
            }
            
        }
        
    }
    //return array of data for output comments
    public function printComments()
    {
        
        $link   = $this->dbConnect();
        $result = mysqli_query($link, "SELECT * from comment ORDER BY id DESC");
        while ($row = $result->fetch_assoc()) {
            $this->addChild($row["id"], $row["parentId"]);
            
            $tmp                    = array();
            $tmp["parentId"]        = $row["parentId"];
            $tmp["name"]            = $row["name"];
            $tmp["text"]            = $row["text"];
            $tmp["date"]            = $row["date"];
            $this->data[$row["id"]] = $tmp;
            
            
        }
        
        $this->printCom($this->tree, 0);
        $tmp = array();
        foreach ($this->index as $key => $value) {
            $tmp2          = array();
            $tmp2          = $this->data[$value['id']];
            $tmp2['level'] = $value['level'];
            $tmp2['id']    = $value['id'];
            array_push($tmp, $tmp2);
        }
        return ($tmp);
    }
    //delete comment $id and all daughterly comments 
   // return id of deleted comments	
    public function deleteComment($id)
    {
        
        $link   = $this->dbConnect();
        $result = mysqli_query($link, "SELECT * FROM comment ORDER BY id DESC");
        while ($row = $result->fetch_assoc()) {
            $this->addChild($row["id"], $row["parentId"]);
        }
        
        if (array_key_exists($id, $this->tree)) {
            
            
            $this->deleteCom($this->tree[$id]);
            
            if (!array_key_exists($id, $this->index))
                array_push($this->index, $id);
            mysqli_query($link, "DELETE  from comment WHERE  `id` IN (" . implode(',', $this->index) . ")");
            return ($this->index);
        }
        
        else {
            mysqli_query($link, "DELETE  from comment WHERE  `id`=$id");
            return $id;
        }
        
        
    }
    
}
?>
