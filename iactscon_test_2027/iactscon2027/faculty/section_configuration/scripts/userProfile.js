/**********************************************************/
/*                REDIRECTION TO EDIT UNIT                */
/**********************************************************/
function openEditScreen(displayDiv, editDiv)
{
	$("#"+displayDiv).css('display','none');
	$("#"+editDiv).css('display','block');
}

/**********************************************************/
/*                  SUBMIT DESIRED FORM                   */
/**********************************************************/
function submitForm(formId)
{
	$("#"+formId).submit();
}

/*********************************************************/
/*                     OPEN POPUP                        */
/*********************************************************/
function openPopUp(fadeDiv, formDiv)
{
	$("#"+fadeDiv).fadeIn("fast");
	$("#"+formDiv).fadeIn("fast");
}

/*********************************************************/
/*                     CLOSE POPUP                       */
/*********************************************************/
function closePopUp(fadeDiv, formDiv)
{
	$("#"+fadeDiv).fadeOut("fast");
	$("#"+formDiv).fadeOut("fast");
}