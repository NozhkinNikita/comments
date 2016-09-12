<?php
	require_once("Comment.class.php");
?>
	<html>
    		<head>
        		<link rel="stylesheet" type="text/css" href="css/style.css">
 			<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
			<script>
			    var answerParentId=0;
			    $(document).ready(function(){
			        PopUpHide();
			    });
	
			    function PopUpShow(id){
			        $("#popup1").show();
			    	answerParentId=id;
			    }

			    function PopUpHide(){
			        $("#popup1").hide();
			    	$("#name2").val('');
			    	$("#commentText2").val('');
			    }

			    function deleteComment(id){
				    $.ajax({
				      url: "ajax.php",
				      type: "POST",
				      data: ({id : id,action:"delete"}),
				      dataType: "html",
				      success: function(data){
					    var id=JSON.parse(data);
					    if(Array.isArray(id))
						    for(var i=0;i<id.length;i++)
						        $("#"+(id[i])).remove();
						    else 
						        $("#"+id).remove();
					      }
				   	     }
					)
			    }

			    function newComment(){
			        var name=$("#name").val();
			    	var text=$("#commentText").val();
			    	var parent=0;
			    	sendComment(name,text,parent);
			    }

			    function answer(){
				    var name=$("#name2").val();
				    var text=$("#commentText2").val();
				    var parent=answerParentId;
				    sendComment(name,text,parent);
			    }

			    function sendComment(name,text,parent)
			    {
			      
				$.ajax({
				      url: "ajax.php",
				      type: "POST",
				      data: ({name : name,text:text,parent:parent}),
				      dataType: "html",
				      success: function(id){
					    var level;
					    if (parent==0)
					        level=1;
					    else{
					    	level= $('#'+parent).attr('class');
					    	level=level[level.length-1];
					    	level++;
					    	if(level>5)
					        	level=5;
					    }
					    if(parent==0)
					        parent="first";
					    var date = new Date();
					    var outDate=date.getFullYear()+'-'+date.getUTCMonth()+'-'+date.getDate()+' '+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();
					    if(level==5){
					     		$('#'+parent).after('<div id='+id+'class="commentsLevel'+level+'"><span class="author">'+name+'</span><span class="date">'+outDate+'</span><hr>'+text.replace(/([^>])\n/g, '$1<br/>')+'<br><span class="answerButton"> </span><span class="date"> <a href="javascript:deleteComment('+id+')";>удалить</a></span>');
					    }
					    else {
					     $('#'+parent).after('<div id='+id+'class="commentsLevel'+level+'"><span class="author">'+name+'</span><span class="date">'+outDate+'</span><hr>'+text.replace(/([^>])\n/g, '$1<br/>')+'<br><span class="answerButton"> <a href="javascript:PopUpShow('+id+')">ответить</a></span><span class="date"> <a href="javascript:deleteComment('+id+')";>удалить</a></span>');
					}
				      }
				   }
				)
				PopUpHide();
			    }
			</script>
    </head>
    <body>    
           <div class="content">
	            <div id="first" class="addComment">
				<div class="addComentWrap">
					<input type=text placeholder="имя" id="name"></input>
					<br>
					<textarea  placeholder="текст" id="commentText"></textarea>
					<br>
					<span class="answerButton">
						<a href="javascript:newComment()">оставить коментарий</a>
					</span>
		     		</div>
            	     </div>

			<?php
			$comment = new Comment();
			$data    = $comment->printComments();
			?>            


			<?php
			foreach ($data as $key => $value) {
			    print('<div id="' . ($value['id']) . '" class="commentsLevel' . ($value['level'] + 1) . '">');
			?>
			<span class="author"><?= $value['name'] ?></span>
			<span class="date"><?= $value['date'] ?></span><hr>
			<?= nl2br($value['text']) ?>
			<br>
			<?php
			    if ($value['level'] < 4)
			        print('<span class="answerButton"><a href="javascript:PopUpShow(' . $value['id'] . ')">ответить</a></span>');
			?>
			<span class="date">
			 <a href="javascript:deleteComment(<?= $value['id'] ?>);">удалить</a>
			</span>
			                </div>
			
			                        
			<?php
			}
			?>
            </div>
<div class="b-popup" id="popup1">
    <div class="b-popup-content">
     <div class="addComentWrap">
		<input type=text placeholder="имя" id="name2"></input>
		<br>
		<textarea  placeholder="текст" id="commentText2"></textarea>
		<br>
		<span class="answerButton">
		<a href="javascript:answer()">ответить</a>
		</span>
		<br>
		<a href="javascript:PopUpHide()">закрыть окно</a>
	</div>
    </div>
</div>

    </body>
</html>
