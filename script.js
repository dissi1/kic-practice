// Валидация формы добавления и редактирования
document.addEventListener('DOMContentLoaded', function() {
    // Находим форму на странице (если она есть)
    const mainForm = document.getElementById('mainForm');
    const editForm = document.getElementById('editForm');
    const activeForm = mainForm || editForm;
    
    if (activeForm) {
        activeForm.addEventListener('submit', function(e) {
            let eventName = document.querySelector('input[name="event_name"]');
            let customer = document.querySelector('input[name="customer"]');
            let deadline = document.querySelector('input[name="deadline"]');
            let hasError = false;
            
            if (eventName && !eventName.value.trim()) {
                eventName.classList.add('error');
                hasError = true;
            } else if (eventName) {
                eventName.classList.remove('error');
            }
            
            if (customer && !customer.value.trim()) {
                customer.classList.add('error');
                hasError = true;
            } else if (customer) {
                customer.classList.remove('error');
            }
            
            if (deadline && !deadline.value) {
                deadline.classList.add('error');
                hasError = true;
            } else if (deadline) {
                deadline.classList.remove('error');
            }
            
            if (hasError) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля');
            }
        });
        
        // Убираем подсветку ошибок при вводе
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('error');
            });
        });
    }
});