/**Authors Kevin Kan and Calin Cohan
 * Date Dec 13 2013
 * This is the login page to access regestered members content.
 */
$(document).ready(function() {
	$("#registerForm").hide();
	$("#toggleLogin").click(function(event){
		event.preventDefault();
		$("#registerForm").slideUp();
		$("#container").slideDown();
	})
	$("#toggleRegister").click(function(event){
		event.preventDefault();
		$("#container").slideUp();
		$("#registerForm").slideDown();
	})
})
