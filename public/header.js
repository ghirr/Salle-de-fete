document.addEventListener('DOMContentLoaded', function() {
    var adminSession = sessionStorage.getItem('admin');
    if (adminSession === 'true') {
      updateHeaderForAdmin();
    }
  
    function updateHeaderForAdmin() {
      var elementsToHide = document.querySelectorAll('.header-element-to-hide');
      elementsToHide.forEach(function(element) {
        element.style.display = 'none';
      });
    }
  });
  