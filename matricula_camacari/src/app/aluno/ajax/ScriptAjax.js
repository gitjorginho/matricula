var xmlHttp, xmlHttp1, layer, filter
    function GetXmlHttpObject()
    {
        var objXMLHttp = null

        if (window.XMLHttpRequest)
        {
            objXMLHttp = new XMLHttpRequest()
        } else if (window.ActiveXObject)
        {
            objXMLHttp = new ActiveXObject("Microsoft.XMLHTTP")
        }
        return objXMLHttp
    }
    function MandaID(filter,layer,str){
     //alert(str);   
     xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Este browser não suporta HTTP Request")
		return
	}

	var url= "../src/app/aluno/ajax/" + layer +".php"
	url=url+"?"+filter+"="+str 
        //alert (url);
	switch(layer){
                case "AjaxGetNotificacaoEscola":
                    xmlHttp.onreadystatechange = stateChangedGetNotificacaoEscola			
		break;			
                case "AjaxSetNotificacaoEscola":
                    xmlHttp.onreadystatechange = stateChangedSetNotificacaoEscola
                    $('#cp_loading').show();
                    $('#putForm').html('');
		break;			
                case "AjaxRetornaEscola":
                    xmlHttp.onreadystatechange = stateChangedRetornaEscola			
		break;			
                
                
	}
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
    }
    function stateChangedGetNotificacaoEscola(){
        if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
            document.getElementById("NotificacaoEscola").innerHTML=xmlHttp.responseText
    }
    function stateChangedSetNotificacaoEscola(){
        if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){
            /*
             *  Carrega o arry com os valores do ajax, impressão do atestado de vaga e a paginação do Sistema
             *  retornoArry[0] = Impresssão do Atestado
             *  retornoArry[1] = Página corrente
             *  retornoArry[2] = Notificação por e-mail
             */
            var retornoArry = JSON.parse(xmlHttp.responseText);
            var reimpressao; 
            //alert(xmlHttp.responseText);
            
            // Tem notificação de E-mail
            if (retornoArry[2] == 'true'){
                reimpressao = 0; //Já houve a emissão do atestado.
            } else{
                reimpressao = 1; // Não houve a emissão do atestado.
            }
            if (retornoArry[0] == 'true'){
                $('#cp_loading').hide();
                getForm('app/aluno/lista_matricular.php?paginacao='+retornoArry[1]+'&voltaredicao=true');    
                window.open('../src/app/aluno/autorizacao_matricula_pdf.php?reimpressao='+reimpressao,'janela');
            }else{
                getForm('app/aluno/lista_matricular.php?paginacao='+retornoArry[1]+'&voltaredicao=true');    
                $('#cp_loading').hide();
            }
       
        }
    }
    function stateChangedRetornaEscola(){
        if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
            document.getElementById("RetornaEscola").innerHTML=xmlHttp.responseText
    }
