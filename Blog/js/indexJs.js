/*Auther Kevin Kan
 * Date Dec 11 2013
 * This jquery handles the captcha functionallity for post comment submissions.
 * */
$(document).ready(function() {//when doc loaded
	$("#allPostsContainer").hide();
	$("#showHidePostButton").click(function(){
		$("#allPostsContainer").slideToggle('slow');
	})
if($("#postCommentForm").length!=0){//if posts are turned on
	var securityKey=newSecurityCode();
	drawSecurityCode(securityKey);
	
	$("#refreshSecurity").click(function(){//refresh the captcha code for click of refresh button
		//alert("refresh");
		securityKey=newSecurityCode();
		drawSecurityCode(securityKey);
	})
	
	$("#submitPostButton").click(function(){//submit form data via ajax if all validation is met
		//
		var postTitle=$("input[name='postCommentTitle']").val();
		var postComment=$("textarea[name='postComment']").val();
		var blogId = $("#postBlogId").val();
		//alert("prevalidation: "+postTitle+" comment: "+postComment+" blogid: "+blogId);
		if(($.trim(postTitle)!="")&&($.trim(postComment)!="")){
			//
			if($("input[name='securityText']").val()==securityKey){
				//alert("ajax: "+postTitle+" comment: "+postComment);
				$.ajax({
					type: "POST",
					url: "php/postFormHandling.php",
					data:{postTitle:postTitle,postContent:postComment,blogId:blogId},
					success: function(){
						location.reload();//reload page to show new post
					},
					error: function (){
						alert("System error occured. Sorry your comment failed to post. If this problem persists please contact the administrator.");
					}
				})
			}
			else{
				$("#refreshSecurity").trigger("click");
				alert("Security Key was Inccorect. Please try again.")
			}
		}
		else{
			alert("Please Fill in all fields.");
		}
	})
	
	function drawSecurityCode(key){//draw captcha text in canvas
		var c2=document.getElementById("securityCanvas");
		var ctx2=c2.getContext("2d");
		ctx2.clearRect(0,0,200,100);
		ctx2.font="30px Arial";
		ctx2.fillText(key,10,50);
	}
	function newSecurityCode(){//random captcha key generator
	    var newKey = '';
	    var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	    for(var i=0; i < Math.ceil(Math.random()*5+4); i++)
	    {
	        newKey += possible.charAt(Math.floor(Math.random() * possible.length));
	    }
	    return newKey;
	}
}
})


