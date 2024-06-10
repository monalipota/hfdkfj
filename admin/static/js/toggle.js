// функ для упр старницами при переключениях
function toggleActive(element) {
  const activeElement = document.querySelector(".pagesec.active");
  if (activeElement) {
    activeElement.classList.remove("active");
    const activeImg = activeElement.querySelector('.pageimg');
    activeImg.classList.remove("active");
  }
  element.classList.add("active");

  // Получаем текст из элемента с классом 'paget' внутри активного элемента
  const text = element.querySelector('.paget').textContent;

  // Находим элемент с классом 'top' и обновляем его содержимое
  const topElement = document.querySelector('.top');
  topElement.textContent = text;

  // Получаем id соответствующего div-элемента контента
  const contentId = element.getAttribute('data-content-id');

  // Скрываем все div-элементы контента
  const contentDivs = document.querySelectorAll('.content-div');
  contentDivs.forEach(div => div.style.display = 'none');

  // Отображаем соответствующий div-элемент контента
  const contentDiv = document.querySelector('#' + contentId);
  contentDiv.style.display = 'block';

  // Добавляем класс 'active' к изображению
  const imgElement = element.querySelector('.pageimg');
  imgElement.classList.add("active");

  var resId = element.getAttribute('data-content-id');
  var topDiv = document.querySelector('.top');
  var button = topDiv.querySelector('button');
  var form = topDiv.querySelector('form');

  if (resId === 'content2' && !form) {
      form = document.createElement('form');
      form.setAttribute('action', './actions/reset-loader-texts.php')
      form.setAttribute('method', 'post')

      button = document.createElement('button');
      button.innerText = 'Сбросить';

      form.appendChild(button);
      topDiv.appendChild(form);
  } else if (contentId !== 'content2' && form) {
      topDiv.removeChild(form);
  }
  
}

// упр активными классами элемнетов меню
const firstPageSec = document.querySelector(".pagesec:first-child");
firstPageSec.classList.add("active");
toggleActive(firstPageSec);

// функ для первого инпута для доллара в конце
function validateInput() {
  var input = document.getElementById('justDol');
  input.value = input.value.replace(/[^\d]/g, '') + '$';
}

// функ для упр инпутами с помощью чекбоксов где нужен доллар
function inputDol() {
  var inputs = document.getElementsByClassName('inputDol');
  for (var i = 0; i < inputs.length; i++) {
      inputs[i].value = inputs[i].value.replace(/[^0-9]/g, ''); // оставить только цифры
      if (inputs[i].value !== '') {
          inputs[i].value += '$'; // добавить знак доллара в конце
      }
  }
}

// функ для упр инпутами где нужны только цифры
function inputNum() {
  var inputs = document.getElementsByClassName('inputNum');
  for (var i = 0; i < inputs.length; i++) {
      inputs[i].value = inputs[i].value.replace(/[^0-9]/g, ''); // оставить только цифры
  }
}

// Вызов функций при изменении значений
var numInputs = document.getElementsByClassName('inputNum');
for (var i = 0; i < numInputs.length; i++) {
  numInputs[i].addEventListener('input', inputNum);
}

var dolInputs = document.getElementsByClassName('inputDol');
for (var i = 0; i < dolInputs.length; i++) {
  dolInputs[i].addEventListener('input', inputDol);
}

// функ для упр инпутами с помощью чекбоксов
window.onload = function() {
  var checkbox1 = document.querySelector('#checkbox1');
  var checkbox2 = document.querySelector('#checkbox2');
  var inputs1 = document.querySelectorAll('.exp1');
  var inputs2 = document.querySelectorAll('.exp2');

  checkbox1.addEventListener('change', function() {
      inputs1.forEach(function(input) {
          input.disabled = !checkbox1.checked;
          input.style.backgroundColor = checkbox1.checked ? 'transparent' : '#1C2029';
      });
  });

  checkbox2.addEventListener('change', function() {
      inputs2.forEach(function(input) {
          input.disabled = !checkbox2.checked;
          input.style.backgroundColor = checkbox2.checked ? 'transparent' : '#1C2029';
      });
  });
}

// функ для огр мин суммы
var inputs = document.querySelectorAll('.inputDol'); 
inputs.forEach(function(input) { // применяем функцию ко всем выбранным элементам input
    input.addEventListener('input', function (e) {
        var num = parseInt(e.target.value, 10);
        
        // Если введено число меньше 10, корректируем его
        if (num < 10) {
            e.target.value = 10;
        }
    });
});

//пикер
document.querySelectorAll('input[type=color]').forEach(function (picker) {

  var targetLabel = document.querySelector('label[for="' + picker.id + '"]'),
      codeArea = document.createElement('span');

  codeArea.innerHTML = picker.value;
  targetLabel.appendChild(codeArea);


  picker.addEventListener('change', function () {
      codeArea.innerHTML = picker.value;
      targetLabel.appendChild(codeArea);
  });
});

//модалки
function setupModalButtons(buttonClass, modalId) {
  var buttons = document.getElementsByClassName(buttonClass);
  var modal = document.getElementById(modalId);
  var span = modal.getElementsByClassName("close")[0];

  for (var i = 0; i < buttons.length; i++) {
      buttons[i].onclick = function() {
          modal.style.display = "block";
      }
  }

  span.onclick = function() {
      modal.style.display = "none";
      document.body.style.overflow = 'auto';
  }

  modal.onclick = function(event) {
      if (event.target == modal) {
          modal.style.display = "none";
          document.body.style.overflow = 'auto';
      }
  }
}



// Используйте функцию для настройки ваших кнопок и модальных окон
setupModalButtons("mod1tar", "myModalId1");
setupModalButtons("mod2tar", "myModalId2");

