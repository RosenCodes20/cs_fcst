
$(document).ready(function() 
{

});

function start_datepicker( lang, date )
{

	$.datepicker.regional['bg'] = {
		clearText: 'Изчистване', clearStatus: '',
		closeText: 'Затваряне', closeStatus: '',
		prevText: 'Предишен месец', prevStatus: '',
		nextText: 'Следващ месец', nextStatus: '',
		currentText: 'Текущ месец', currentStatus: '',
		monthNames: ['Януари','Февруари','Март','Април','Май','Юни', 'Юли','Август','Септември','Октомври','Ноември','Декември'],
		monthNamesShort: ['Янр','Фев','Мрт','Апр','Май','Юни','Юли','Авг','Спт','Окт','Нвр','Дек'],
		monthStatus: '', yearStatus: '',
		weekHeader: '', weekStatus: '',
		dayNames: ['Неделя','Понеделник','Вторник','Сряда','Четвъртък','Петък','Събота'],
		dayNamesShort: ['Нед','Пон','Вто','Сря','Чет','Пет','Съб'],
		dayNamesMin: ['Нд','Пн','Вт','Ср','Чт','Пт','Сб'],
		dayStatus: '', dateStatus: '',
		dateFormat: 'dd/mm/yy', firstDay: 1, 
		initStatus: '', isRTL: false
	};

	$.datepicker.regional['en'] = {
		dateFormat: 'dd/mm/yy', firstDay: 0, 
		initStatus: '', isRTL: false
	};

	$.datepicker.setDefaults($.datepicker.regional[lang]);

	var x = new Date();
	
	$('#datepicker').datepicker({ 
		beforeShowDay: highlight,
		minDate: new Date(x.getFullYear(), x.getMonth(), 01),
		onSelect: function(dateText, inst) { 
			document.location.href = 'events-list.php?date=' + dateText;
		}
	});
	
	if (date) {
	
		$('#datepicker').datepicker("setDate", date );
	}
	
	refresh_datepicker_popups();
	
	$('.ui-datepicker-next').live("click", function() {
	
		refresh_datepicker_popups();
	});
	
	$('.ui-datepicker-prev').live("click", function() {
	
		refresh_datepicker_popups();
	});

	$('#search').blur(function() {
	
		var item = document.getElementById('search');

		if (item.value.trim() == '') {
		
			item.value = c_search;
			item.className = "search-field color-blur";
		}
	});
}

var handler_blink = false;

function highlight(date) 
{
	var nDate = date.getDate();
	var nMonth = date.getMonth() + 1;
	var nYear = date.getFullYear();
	
	nDate = nDate < 10 ? '0' + nDate : nDate;
	nMonth = nMonth < 10 ? '0' + nMonth : nMonth;

	var highlight = SelectedDates[ '' + nDate + '/' + nMonth + '/' + nYear ];

   return [true, highlight ? 'datepicker-event' : ''];
}

function refresh_datepicker_popups()
{
	$('.datepicker-event').each( function( key, $elem ){
		$(this).attr( 'title', "Вижте събитията за този ден" );
	});
}

/*
$(document).ready(function() {
    var SelectedDates = {};
    SelectedDates[new Date('04/05/2012')] = new Date('04/05/2012');
    SelectedDates[new Date('05/04/2012')] = new Date('05/04/2012');
    SelectedDates[new Date('06/06/2012')] = new Date('06/06/2012');
 
    $('#txtDate').datepicker({
        beforeShowDay: function(date) {
            var Highlight = SelectedDates[date];
            if (Highlight) {
                return [true, "Highlighted", Highlight];
            }
            else {
                return [true, '', ''];
            }
        }
    });
});
*/

var blink_switch = false;

function blink( item )
{
	if (!handler_blink) {

		handler_blink = setInterval( function() {
			
			if (blink_switch) {
			
				if (jQuery.browser.msie) {
					
					item.style.visibility = 'hidden';
					
				} else {
				
					item.style.opacity = "0.00";
					item.style.filter = "alpha(opacity=0)";
				}
				
				blink_switch = false;
				
			} else {
			
				if (jQuery.browser.msie) {
					
					item.style.visibility = 'visible';
					
				} else {
				
					item.style.opacity = "1.00";
					item.style.filter = "alpha(opacity=100)";
				}

				blink_switch = true;
			}
			
		}, 100);
	}
	

}

function reload_captcha()
{
	var captcha = document.getElementById('captcha');
	var src = captcha.src;
	
	captcha.src = '';
	captcha.src = src + "#";
}

function search_submit()
{
	var q = document.getElementById("search");
	
	var win = window.open("https://www.google.bg/search?q=cs.tu-sofia.bg " + q.value, '_blank');
	win.focus();	
	
	return false;
}

var c_search = false;

function clear_search()
{
	var item = document.getElementById('search');

	if (!c_search)
		c_search = item.value;

	if (c_search == item.value) {
	
		item.value = '';
		item.className = "search-field color-focus";
	}
}