// @author Eyob Ayalew 




$('#add-form').submit(function(event) {
    event.preventDefault();
    var priority = $('#priority').val();
    var formData = new FormData(this);
    formData.append('priority', priority);
    
    $(this).find(':input').prop('disabled', true);
    
    fetch('app/add.php', {
        method: 'POST',
        body: formData
    }).then(response => {

        console.log(response);
        
        updateTodoList();
        
        
        $(this).find(':input').val('');
        
        $(this).find(':input').prop('disabled', false);
    }).catch(error => {
        
        console.error('Error:', error);
        
        $(this).find(':input').prop('disabled', false);
    });
});