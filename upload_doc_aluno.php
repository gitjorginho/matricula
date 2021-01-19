<?php
session_start();
header("Content-Type: text/html;  charset=ISO-8859-1", true);

/******
 * Upload de imagens
 ******/
 
function uploadImagemDocAluno($files,$id_aluno){
    $arrDocumentoAluno = array();

    foreach ($_FILES as $key => $value){
         $input = $key;
         
    
         // verifica se foi enviado um arquivo
        if ( isset( $_FILES[ $input ][ 'name' ] ) && $_FILES[ $input ][ 'error' ] == 0 ) {
       // echo 'Você enviou o arquivo: <strong>' . $_FILES[ $input ][ 'name' ] . '</strong><br />';
       // echo 'Este arquivo é do tipo: <strong > ' . $_FILES[ $input ][ 'type' ] . ' </strong ><br />';
       // echo 'Temporáriamente foi salvo em: <strong>' . $_FILES[ $input ][ 'tmp_name' ] . '</strong><br />';
       // echo 'Seu tamanho é: <strong>' . $_FILES[ $input ][ 'size' ] . '</strong> Bytes<br /><br />';
     
        $arquivo_tmp = $_FILES[ $input ][ 'tmp_name' ];
        $nome = $_FILES[ $input ][ 'name' ];
     
        // Pega a extensão
        $extensao = pathinfo ( $nome, PATHINFO_EXTENSION );
     
        // Converte a extensão para minúsculo
        $extensao = strtolower ( $extensao );
     
        // Somente imagens, .jpg;.jpeg;.gif;.png
        // Aqui eu enfileiro as extensões permitidas e separo por ';'
        // Isso serve apenas para eu poder pesquisar dentro desta String
        if (strstr ( '.jpg;.jpeg;.gif;.png;.pdf;.tif', $extensao )) {
            // Cria um nome único para esta imagem
            // Evita que duplique as imagens no servidor.
            // Evita nomes com acentos, espaços e caracteres não alfanuméricos
            $novoNome = uniqid ( time () ) . '.' . $extensao;
            
            // nome da pasta para cada aluno
            $sNomePastaAluno =  'aluno'.$id_aluno;
            
            // verifica se a pasta ja existe
           if (!is_dir("imagens_doc_aluno/$sNomePastaAluno")){
                mkdir("imagens_doc_aluno/$sNomePastaAluno", 0700);
            }        
                        // Concatena a pasta com o nome
                $destino = "imagens_doc_aluno/$sNomePastaAluno/$input.$novoNome";
           
                // tenta mover o arquivo para o destino
                if ( @move_uploaded_file ( $arquivo_tmp, $destino ) ) {
                    //salvou arquivo com sucesso
                    $dadosDoc = explode('-',$key);
                    $dadosNomeDoc =  $dadosDoc[1].'-'.$dadosDoc[2];
                    array_push($arrDocumentoAluno,array("id_documentoreserva"=>$dadosDoc[0],"nome_documento"=>$input.$novoNome,"caminho_documento"=> $destino));             
                }
                //else
                // return false;
                // echo 'Erro ao salvar o arquivo. Aparentemente você não tem permissão de escrita.<br />';
           }
        //else
         //return false;
            //echo 'Você poderá enviar apenas arquivos "*.jpg;*.jpeg;*.gif;*.png"<br />';
    }
   //else
    //return false;
        //echo 'Você não enviou nenhum arquivo!';
    }
    
    if(count($arrDocumentoAluno) == 0){
          return false;
    }
    
    return  $arrDocumentoAluno;
}




