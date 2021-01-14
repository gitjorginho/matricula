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
		alert ("Este browser n�o suporta HTTP Request")
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
             *  Carrega o arry com os valores do ajax, impress�o do atestado de vaga e a pagina��o do Sistema
             *  retornoArry[0] = Impresss�o do Atestado
             *  retornoArry[1] = P�gina corrente
             *  retornoArry[2] = Notifica��o por e-mail
             */
            var retornoArry = JSON.parse(xmlHttp.responseText);
            var reimpressao; 
            //alert(xmlHttp.responseText);
            
            // Tem notifica��o de E-mail
            if (retornoArry[2] == 'true'){
                reimpressao = 0; //J� houve a emiss�o do atestado.
            } else{
                reimpressao = 1; // N�o houve a emiss�o do atestado.
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
