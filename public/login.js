document.addEventListener('DOMContentLoaded', function() {
    var userNameInput = document.querySelector('.login__input[name="username"]');
    var passwordInput = document.querySelector('.login__input[name="password"]');    
  
    document.addEventListener('submit', function(event) {
      var loginForm = event.target.closest('.login');
      if (loginForm) {
        event.preventDefault();
  
        var userName = userNameInput.value;
        var password = passwordInput.value;
  
        if (userName === 'admin' && password === 'admin') {
          sessionStorage.setItem('admin', 'true');
          window.location.href = '/dash';
        } else {
          console.log('Identifiant ou mot de passe incorrect');
        }
      }
    });
  
    var adminSession = sessionStorage.getItem('admin');
    if (adminSession === 'true') {
      updateHeaderForAdmin();
    }
  });
  
  function updateHeaderForAdmin() {
    var elementsToHide = document.querySelectorAll('.header-element-to-hide');
    elementsToHide.forEach(function(element) {
      element.style.display = 'none';
    });
  }
  