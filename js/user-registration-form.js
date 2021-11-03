'use strict';
function Form(formtohandle) {
	this.theform=document.getElementById(formtohandle);
	this.firstname=new TextInput(this.theform.firstname);
	this.surname=new TextInput(this.theform.surname);
	this.username=new LSInput(this.theform.username);
	this.email=new EmailInput(this.theform.email);
	this.pass=new PassInput(this.theform.userpass);
	this.dob=new DateInput(this.theform.dob);
	this.XHR=this.createXHR();
	this.theform.addEventListener("submit", this.checkSubmit.bind(this), false);
	
};
	Form.prototype.checkSubmit=function(e) {
		e.preventDefault();
		var fn,sn,un,em,pw,db = false;
		fn=this.firstname.check();
		sn=this.surname.check();
		un=this.username.check();
		em=this.email.check();
		pw=this.pass.check();
		db=this.dob.check();
		if(fn&&sn&&un&&em&&db&&pw) {
			if(typeof JSON==="object") {
				this.checkUser();
			} else {
				this.aform.submit();
			}
		}

	};

	/***************************
	* Create XHR - supported from IE9
	* ActiveXObject included as example
	***************************/
	Form.prototype.createXHR=function() {
		if (window.XMLHttpRequest) {
			return new XMLHttpRequest();
		}
		else if(window.ActiveXObject) {
			return new ActiveXObject("Microsoft.XMLHTTP");
		}
	};

	/***************************
	* AJAX send of username and password
	* for checking validity and uniqueness
	***************************/	
	Form.prototype.checkUser=function() {
		this.theform.submitbutton.disabled=true;
		this.XHR.open("POST","../php/ajax-handlers/checkuser.php",true);
		this.XHR.onreadystatechange = this.availabilityHandler.bind(this);
		// Send request
		this.XHR.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		this.XHR.send("username="+encodeURIComponent(this.username.getValue())+"&email="+encodeURIComponent(this.email.getValue()));
	};
	
	/**************************
	* Handles response from XHR readystate changes
	**************************/
	Form.prototype.availabilityHandler=function() {
		if(this.XHR.readyState==4) {
			var useravailable=true;
			if(this.XHR.status==200) {
				if(this.XHR.response=="Could not connect to database: 1045")
				{
					// Database connectivity issues
					useravailable=false;
					this.username.doFeedback(useravailable,"Could not process request, try again later");
					
				} else {
					// Get json array returned, 0 or 1 users
					var responsedata=JSON.parse(this.XHR.responseText);
					// Will be 0 if there are no matching users
					switch(parseInt(responsedata.userexists)) {
						case 0:
							this.username.doFeedback(true,"");break;
						case 1:
							useravailable=false;
							this.username.doFeedback(useravailable,"Username has already been registered");
							break;
						case 2:
							useravailable=false;
							this.username.doFeedback(useravailable,"Invalid Username");
							break;
						default:
							useravailable=false;
							this.username.doFeedback(useravailable,"Invalid Request");
							break;
					}
					switch(parseInt(responsedata.emailexists)) {
						case 0:
							this.email.doFeedback(true,"");break;
						case 1:
							useravailable=false;
							this.email.doFeedback(useravailable,"Email not available");
							break;
						case 2:
							useravailable=false;
							this.email.doFeedback(useravailable,"Invalid Email");
							break;
						default:
							useravailable=false;
							this.email.doFeedback(useravailable,"Invalid Request");
							break;
					}
				}
			} else if(this.XHR.status>=400) {
				useravailable=false;
				this.username.doFeedback(useravailable,"Could not process request");
			}
			if(useravailable) {
				$('#submitbutton').html("<img style='width:28px;' src='/assets/loader-button.gif'>");
				this.theform.submit()
			}
			this.theform.submitbutton.disabled=false;
		}
	};
		
/*************************
* Text Input Class
*************************/
function TextInput(i) {
	this.hte=i;
	this.id=i.getAttribute("id");
	this.fbid=this.id+"fb";
};

	/***************************
	* Return current input box value
	***************************/
	TextInput.prototype.getValue=function() {
		return this.hte.value;
	};
	
	/***************************
	* Basic Input box validation
	***************************/	
	TextInput.prototype.check=function() {
		var checkstatus=true;
		if(this.getValue()==null || this.getValue().trim()==="") {
			checkstatus=false;
		}
		this.doFeedback(checkstatus, "This field cannot be empty");
		return checkstatus;
	};
	
	/***************************
	* Default feedback mechanism for input boxes
	***************************/	
	TextInput.prototype.doFeedback=function(checkstatus,failmessage) {
		var fbtarget=document.getElementById(this.fbid);
		if(checkstatus) {
			if(fbtarget) {
				fbtarget.parentNode.removeChild(fbtarget);
				this.hte.style.backgroundColor="#fff";
			}
		} else {
			if(!fbtarget) {
				var fbtarget=document.createElement("p");
				fbtarget.setAttribute("id",this.fbid);
				this.hte.parentElement.insertAdjacentElement('afterend',fbtarget);
				this.hte.style.backgroundColor="#f99";
			}
			fbtarget.innerHTML=failmessage;
			
		}
	};
	
/*************************
* Limited String Input Class
*************************/
function LSInput(i) {
	this.pattern=/^[A-Z0-9]{4,15}$/i
	TextInput.call(this, i);
};

	/***************************
	* LS Input box constructor extension
	***************************/
	LSInput.prototype = Object.create(TextInput.prototype);
	LSInput.prototype.constructor = LSInput;

	/***************************
	* LS validation against test pattern
	***************************/	
	LSInput.prototype.check=function() {
		var checkstatus=TextInput.prototype.check.call(this);
		if(checkstatus) {
			checkstatus=(this.pattern.test(this.getValue()));
		}
		this.doFeedback(checkstatus, "Must be 6 to 15 letters and numbers");
		return checkstatus;
		
	};

/*************************
* Email Input Class
*************************/
function EmailInput(i) {
	LSInput.call(this, i);
	this.pattern=/^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/
};	
	/***************************
	* Email Input box constructor extension
	***************************/
	EmailInput.prototype = Object.create(LSInput.prototype);
	EmailInput.prototype.constructor = EmailInput;
	
	/***************************
	* Simple Email validation against test pattern
	***************************/	
	EmailInput.prototype.check=function() {
		var checkstatus=LSInput.prototype.check.call(this);
		this.doFeedback(checkstatus, "Invalid email format");
		return checkstatus;
	};

/*************************
* Password field class
*************************/
function PassInput(i) {
	this.passmark=0;
	this.strfb=this.id+"strfb";
	this.feedbackcolour="#F00";
	TextInput.call(this,i);	
	this.hte.addEventListener("keyup", this.checkStr.bind(this), false);
}
	/***************************
	* Password Input constructor extension
	***************************/
	PassInput.prototype = Object.create(TextInput.prototype);
	PassInput.prototype.constructor = PassInput;
	
	/***************************
	* Generate a complexity score for passwords
	***************************/	
	PassInput.prototype.checkStr=function(e) {
		// Baseline output colour of red for weak passwords
		var currpass=this.getValue();
		// Get the total length of password, baseline strength of 1 for a 6 character password
		var passtr=(currpass.length<6)?1:currpass.length/2;
		// Use Regex to find what types of characters, symbols and numbers used
		var hassymbol=((/[-!£$%^&*()_+|~=`{}\[\]:";'<>?,.\/]/).test(currpass))?1.5:0;
		var hasnumeric=((/[0-9]/).test(currpass))?1.4:0;
		var hasupper=((/[A-Z]/).test(currpass))?1.2:0;
		var haslower=((/[a-z]/).test(currpass))?1.1:0;
		// Calculate the overall relative strength of the password
		this.passmark=passtr*(hassymbol+hasnumeric+hasupper+haslower);
		if(this.passmark>24) { this.passmark=24; }
		// Yellow colour for medium strength passwords
		if(this.passmark>8) { this.feedbackcolour="#FF0"; }
		// Green colour for strong passwords
		if(this.passmark>16) { this.feedbackcolour="#0F0"; }
		// Red for weak
		if(this.passmark<8) { this.feedbackcolour="#F00";}
		this.doStrFeedback();
	};

	/***************************
	* Custom feedback mechanism for password strength
	***************************/	
	PassInput.prototype.doStrFeedback=function() {
		var fbtarget=document.getElementById(this.strfb);
		if(!fbtarget) {
			var fb=document.createElement("p");
			fb.setAttribute("id",this.strfb);
			fb.classList.add("password-indicator");
			this.hte.parentElement.insertAdjacentElement('afterend',fb);
			fbtarget=document.getElementById(this.strfb);
			fbtarget.style.display="block";
			fbtarget.style.height="1em";
		}
		fbtarget.style.backgroundColor=this.feedbackcolour;
		fbtarget.style.width=this.passmark+"em"
	}

	/***************************
	* Password validation against complexity
	***************************/	
	PassInput.prototype.check=function() {
		var checkstatus=(this.passmark<8)?false:true;
		this.doFeedback(checkstatus, "Password is not complex enough");
		return checkstatus;
		
	};	
	
/*************************
* Date Input Class
*************************/
function DateInput(i) {
	this.day=null;
	this.month=null;
	this.year=null;
	TextInput.call(this,i);
	this.isadate=true;
	if(!this.checkDateSupport()) {
		this.isadate=false;
		this.fixDate();
	}
	if(this.getValue()!=="" && !this.isadate) {
		this.setupExisting();
	}

	console.log(this.isadate);

};

	/***************************
	* Date Input constructor extension
	***************************/
	DateInput.prototype = Object.create(TextInput.prototype);
	DateInput.prototype.constructor = DateInput;

	/***************************
	* Input and Age Check
	***************************/	
	DateInput.prototype.check=function() {
		var checkstatus=TextInput.prototype.check.call(this);
		if(checkstatus) {
			var today = new Date();
			var datearray=this.hte.value.split("-");
			var birthdate = new Date(datearray[0], datearray[1]-1, datearray[2]);
			var age = today.getFullYear() - birthdate.getFullYear();
			var m = today.getMonth() - birthdate.getMonth();
			if (m < 0 || (m == 0 && today.getDate() < birthdate.getDate())) {
				age--;
			}
			checkstatus=age>=18;
		}		this.doFeedback(checkstatus,"You must be at least 18 to sign up");
		return checkstatus;
	};
	
	/***************************
	* Date Input box type check - If testelement date is set
	* .value == garbagevalue which means date is operating as
	* text field := unsuppported browser
	***************************/
	DateInput.prototype.checkDateSupport=function() {
		var result=false;
        var testelement = document.createElement('input');
        var garbagevalue = 'garbage';
        testelement.setAttribute('type','date');
        testelement.setAttribute('value', garbagevalue);
        result=(testelement.value !== garbagevalue);
		return result;
	};

	/***************************
	* If Date input type check fails generates
	* three select boxes to maintain ease of use
	***************************/	
	DateInput.prototype.fixDate=function() {
		this.hte.setAttribute("type", "hidden");
		this.day = new DayElement(this.id);
		this.year= new YearElement(this.id);
		this.month= new MonthElement(this.id);
		this.hte.insertAdjacentElement('beforebegin',this.year.hte);
		this.year.hte.insertAdjacentElement('beforebegin',this.month.hte);
		this.month.hte.insertAdjacentElement('beforebegin',this.day.hte);
		this.day.hte.addEventListener("change", this.setDays.bind(this), false);
		this.month.hte.addEventListener("change", this.setDays.bind(this), false);
		this.year.hte.addEventListener("change", this.setDays.bind(this), false);

	};
	
	/***************************
	* If selects being used this will limit the
	* number of days available in days select box
	* dependent on month / leap year
	***************************/	
	DateInput.prototype.setDays=function(e) {
		var currentyear=parseInt(this.year.hte.options[this.year.hte.selectedIndex].value);
		var currentmonth=parseInt(this.month.hte.options[this.month.hte.selectedIndex].value);
		// 31 days default, on february check for leap year
		// case fallthrough used to capture months with 30 days
		var maxdays=31;
		switch(currentmonth) {
			case 2:
				maxdays=((currentyear%4)==0&&((currentyear%400)==0 || (currentyear%100)!==0))?29:28;
				break;
			case 4:
			case 6:
			case 9:
			case 11:
				maxdays=30;
				break;
		}
		if(maxdays>this.day.hte.length) {
			// add some options days 29 -> 31 as needed
			var startday=this.day.hte.length+1;
			for(var i=startday;i<=maxdays;i++) {
				var theoption=document.createElement("option");
				theoption.text=i;
				theoption.value=i;
				this.day.hte.add(theoption);
			}
		} else if (maxdays<this.day.hte.length) {
			// remove some options days 31 -> 29 as needed
			for(var i=this.day.hte.length;i>=maxdays;i--) {
				this.day.hte.remove(i);
			}
		}
		this.setDate();
	};
	
	/***************************
	* Set primary Date Input box from three
	* select boxes of year, month, day
	***************************/	
	DateInput.prototype.setDate= function(){
		this.hte.value=this.year.hte.options[this.year.hte.selectedIndex].value+"-"+this.month.hte.options[this.month.hte.selectedIndex].value+"-"+this.day.hte.options[this.day.hte.selectedIndex].value;
	};

	DateInput.prototype.setupExisting=function() {
		var datearray=this.getValue().split("-");
		this.year.setCurrent(datearray[0]);
		this.month.setCurrent(datearray[1]);
		this.day.setCurrent(datearray[2]);
	}

/***************************
* Day Element class
***************************/	
function DayElement(srcid) {
	this.hte=document.createElement('select');
	this.hte.setAttribute("id",srcid+"dayofmonth");
	this.hte.setAttribute("name",srcid+"dayofmonth");
	for(var i=1;i<=31;i++) {
		var theoption=document.createElement("option");
		theoption.text=i;
		theoption.value=i;
		this.hte.add(theoption);
	}
	this.hte.selectedIndex=0;
}
	DayElement.prototype.setCurrent=function(day) {
		this.hte.selectedIndex=day-1;
	}
	
/***************************
* Month Element class
***************************/
function MonthElement(srcid) {
	this.hte=document.createElement('select');
	this.hte.setAttribute("id", srcid+"month");
	this.hte.setAttribute("name", srcid+"month");
	var montharray=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	for(var i=0;i<=11;i++) {
		var theoption=document.createElement("option");
		theoption.value=i+1;
		theoption.text=montharray[i];
		this.hte.add(theoption);
	}
	this.hte.selectedIndex=0;
}
	MonthElement.prototype.setCurrent=function(month) {
		this.hte.selectedIndex=month-1;
	}

/***************************
* Year Element class
***************************/
function YearElement(srcid) {
	this.earliestyear=1901;
	this.hte=document.createElement('select');
	this.hte.setAttribute(srcid+"id","year");
	this.hte.setAttribute(srcid+"name","year");
	var d = new Date();
	var currentyear=d.getFullYear();
	for(var i=this.earliestyear;i<currentyear;i++) {
		var theoption=document.createElement("option");
		theoption.text=i;
		theoption.value=i;
		this.hte.add(theoption);
	}
	var defaultyear=this.hte.length-20;
	this.hte.selectedIndex=defaultyear;
}
	YearElement.prototype.setCurrent=function(year) {
		this.hte.selectedIndex=year-this.earliestyear;
	}
