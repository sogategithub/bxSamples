elementMassCross={
	item:{},
	that:{},
	init:function(e){
		this.block=e;
		that=this;
		$(document).on("click",".element_mass_cross_buttons a",this.click);
		$(document).on("change",".element_mass_cross_buttons input[type='file']",this.upload);
		},
	upload:function(f){
			let formData = new FormData();
			formData.append("file", this.files[0]);
			let e=that.getSettings();
			e.action="put";
			e.success=function(e){
				that.setWait();
				if(typeof(e.data)=="undefined"){
					return;
					}
				that.open({
					title:that.block.attr("data-done"),
					message:that.block.attr("data-message")+e.data.rows_processed
					});
				};
			e.data=formData;
			that.request(e);
			this.value="";
			},
		base64ToBlob:function(base64, mimetype, slicesize) {
			if (!window.atob || !window.Uint8Array) {
				// The current browser doesn't have the atob function. Cannot continue
				return null;
				}
			mimetype = mimetype || '';
			slicesize = slicesize || 512;
			var bytechars = atob(base64);
			var bytearrays = [];
			for (var offset = 0; offset < bytechars.length; offset += slicesize) {
				var slice = bytechars.slice(offset, offset + slicesize);
				var bytenums = new Array(slice.length);
				for (var i = 0; i < slice.length; i++) {
								bytenums[i] = slice.charCodeAt(i);
				}
			var bytearray = new Uint8Array(bytenums);
			bytearrays[bytearrays.length] = bytearray;
			}
			return new Blob(bytearrays, {type: mimetype});
		},
	giveFile:function(e){
		that.setWait();
		var a = document.createElement('a');
		if (window.URL && window.Blob && ('download' in a) && window.atob) {
			// Do it the HTML5 compliant way
			var blob = that.base64ToBlob(e.data, "text/csv");
			var url = window.URL.createObjectURL(blob);
			a.href = url;
			a.download = "cross.csv";
			a.click();
			window.URL.revokeObjectURL(url);
			}
		},
	getSettings:function(){
		return {
			component:that.block.attr("data-component"),
			error:that.error,
			data:{
				sessid:BX.message('bitrix_sessid'),
				signedParameters:that.block.attr("data-params"),
				}
			};
		},
	download:function(){
		let e=that.getSettings();
		e.action="get";
		e.success=that.giveFile;
		that.request(e);
		},
	setWait:function(e){
		if(e===true){
			$(that.item).prop("disabled",true);
			BX.showWait();
			}
		else{
			$(that.item).prop("disabled",false);
			BX.closeWait();
			}
		},
	request:function(e){
		that.setWait(true);
		BX.ajax.runComponentAction(e.component,e.action,{
			mode:'class',
			data:e.data
			})
		.then(
			e.success,
			e.error
			);
		},
	errorMessage:function(e){
		return{
			errors:[{message:e}]
			};
		},
	error:function(e){
		let c=[];
		if(!e)
			c.push(that.block.attr("data-undefinedError"));
		else if(!e.errors)
			c.push(that.block.attr("data-undefinedError"));
		else{
			for(i in e.errors){
				c.push(e.errors[i].message);
				}
			}
		that.notify(that.block.attr("data-error")+c.join(''));
		that.setWait(false);
		},
	notify:function(e){
		BX.UI.Notification.Center.notify({
			content:e,
			position: "bottom-center"
			});
		},
	dialog:BX.UI.Dialogs.MessageBox.create({
		modal:true,
		minWidth:320,
		}),
	open:function(e){
		that.dialog.setTitle(e.title);
		that.dialog.setMessage(e.message);
		that.dialog.setButtons(BX.UI.Dialogs.MessageBoxButtons.CANCEL);
		that.dialog.show();
		},
	click:function(e){
		that.item=this;
		if($(that.item).hasClass("ui-btn-danger")){
			$(that.item).next().click();
			}
		else{
			that.download();
			}
		e.preventDefault();
		}
	};
$(document).ready(function(){
	elementMassCross.init($(".element_mass_cross_buttons"));
	});