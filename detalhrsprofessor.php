<div class="container mt-3">
<?php 

if(isset($_GET['id_chamado']))
{
	$id = $_GET['id_chamado'];
}else{
	$id = 0;
}



?>
 <!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" data-bs-toggle="tab" href="#detalhe">Detalhes</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#comentario">Comentários</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-bs-toggle="tab" href="#anexo">Anexos</a>
  </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
<?php 
   include_once("classes/funcoessql.php");
   //objeto para tratar o select dos chamados
   $detalhes = new funcoessql();
				   $detalhes->setconsulta("SELECT ch.id_chamado, ch.assunto, ch.descricao_chamado, cat.desc_categoria,
					   sta.desc_status, pri.desc_prioridade, usu.nome,
					   DATE_FORMAT(data_abertura,'%d/%m/%Y') data_abertura, id_status, id_prioridadae, id_categoria
				 FROM categoria  cat,
					  usuario    usu,
					  status     sta,
					  prioridade pri,
					  chamado    ch
				WHERE id_chamado        = ".$id."
				  and ch.fk_categoria   = cat.id_categoria 
				  and ch.fk_usuario     = usu.id_usuario
				  and ch.fk_status      = sta.id_status
				  and ch.fk_prioridadae = pri.id_prioridadae");
 if($detalhes->total() > 0)
 {	
    //varre o resultado do select e mostra o resultado na div detalhe
     foreach( $detalhes->ler() as $d )
	 {
         if( $_SESSION['tipo_user'] == "USUARIO" )
		{
                echo "
                <div class='tab-pane container active' id='detalhe'>
                <h6>Categoria: <select>";

                $categorias = new funcoessql();
                $categorias->setconsulta("SELECT id_categoria, desc_categoria FROM categoria");

                foreach( $categorias->ler() AS $c ):
                    if( $d['id_categoria'] == $c['id_categoria'] ):
                          echo "<option value=".$c['id_categoria'].">".$c['desc_categoria']."</option>";
                    else:
                          echo "<option >"<$c['desc_categoria']."</option>";
                    endif;
                      
                endforeach;
                echo "</select><br>";
			  /*Status: ".$d['desc_status']." <br>
			  Prioridade: ".$d['desc_prioridade']."<br>
		  Aberto por: ".$d['nome']."<br>
		  Em:".$d['data_abertura']."<br></h6>
		  <h3>#".$id." - ".$d['assunto']."</h3>
		  <h4>".$d['descricao_chamado']."</h4>
		  </div>";*/
		 }elseif( $_SESSION['tipo_user'] == "ANALISTA" ){
		 	 
			 echo "		 
		     <div class='tab-pane container active' id='detalhe'> <h6>
			 <form class=form-control method=post action=manter_detalhes.php?id=$id>
			 <input type=text value=".$d['id_categoria']." name=cat_atual></input>
			 <input type=text value=".$d['id_status']." name=st_atual></input>
			 <input type=text value=".$d['id_prioridadae']." name=pri_atual></input>";
		    
		    echo "Categoria: <select class=form-select name=newcategoria><br>"; 
			$ct = new funcoessql();
			$ct->setconsulta("SELECT id_categoria, desc_categoria FROM categoria");
			foreach( $ct->ler() as $c ):
			   if( $d['id_categoria'] == $c[0] )
			   {
                                echo "<option value=$c[0] selected>$c[1]</option>";
			   }else{
                                echo "<option value=$c[0]>$c[1]</option>";
			   }
			endforeach;
			
             echo "</select>";
     		
			
		     echo "<br>Status:<select class=form-select name=newstatus>";
			 $st = new funcoessql();
			 $st->setconsulta("SELECT * FROM status");
			 foreach($st->ler() as $s) :
				 if($d['id_status'] == $s[0])
				 {
					echo "<option value=$s[0] selected>$s[1]</option>"; 
				 }else{
					echo "<option value=$s[0]>$s[1]</option>"; 
				 }
			 endforeach;
			 echo "</select>";
			 
			echo "<br>Prioridade: <select class=form-select name=newprioridade>";
			$pr = new funcoessql();
			$pr->setconsulta("SELECT * FROM prioridade");
			foreach($pr->ler() as $p):
                            
			   if($d['id_prioridadae']==$p[0])
			   {
				   echo "<option value=$p[0] selected>$p[1]</option>";
			   }else{
				   echo "<option value=$p[0]>$p[1]</option>";
			   }
                           
			endforeach;
		   
		   echo "</select><br>
		         <button type='submit' name=atualizar class='btn btn-primary'>Atualizar</button>
				 </form>
		         <br>Aberto por: ".$d['nome']."<br>
		         Em:".$d['data_abertura']."<br></h6>
		         <h3>#".$id." - ".$d['assunto']."</h3>
		         <h4>".$d['descricao_chamado']."</h4>
		         </div>";
			 
		 }
	 }
 }	 
 
   //objeto para tratar o select dos comentários 
   $comentario = new funcoessql();
   $comentario->setconsulta("SELECT usu.nome, 
					   cm.comentario, 
					   DATE_FORMAT(cm.data_comentario,'%d/%m/%Y') data_comentario
				FROM usuario usu, comentario cm
				WHERE cm.fk_usuario = usu.id_usuario
				and cm.fk_chamado   = ".$id."
				ORDER BY cm.data_comentario DESC");
    echo "<div class='tab-pane container fade' id='comentario'>";
	
	if($comentario->total() > 0)
	{
	 
		 
		 //varre o resultado do select e mostra o resultado na div comentario
		  foreach( $comentario->ler() as $c ):
                      
			 echo "Em ".$c[2]." o usuário <b>".$c[0]."</b> escreveu:<br>
			 ".$c[1]." <hr> ";
                  endforeach;
		
	}
	?>
	<div class="container mt-3">
	  <h2>Digite o seu comentário</h2>
	  <?php 
	     echo "<form method=post action=manter_detalhesprofessor.php?id=$id>";
	  ?>
	  
		<div class="mb-3 mt-3">
		  <textarea class="form-control"  maxlength=1000 rows="5" id="comment" name="comentario">  </textarea>
		</div>
		<button type="submit" name=enviar class="btn btn-primary">Enviar</button>
	  </form>
	</div>

	
    <?php
	//objeto para tratar o select dos anexos
	 echo "</div>";
	$anexo = new funcoessql();
	$anexo->setconsulta("SELECT arq.nome_arquivo ,
					   usu.nome,
					   DATE_FORMAT(arq.data_inclusao,'%d/%m/%Y') data_inclusao
				FROM arquivo arq,
					 usuario usu
				WHERE arq.fk_usuario = usu.id_usuario 
				  AND arq.fk_chamado = ".$id."
				ORDER BY arq.data_inclusao DESC");
	if($anexo->total() > 0)
	{
		echo "<div class='tab-pane container fade' id='anexo'>";
		//varre o resultado do select e mostra o resultado na div anexo
		foreach($anexo->ler() as $l)
		{
	      echo "Em ".$l['data_inclusao']." o usuário ".$l['nome']." anexou: <br> 
		  <a href=arquivo/".$l['nome_arquivo']." download=arquivo/".$l['nome_arquivo']." target='_blank'>".$l['nome_arquivo']." </a><hr>";
		}//fecha o foreach
	}//fecha se total>0
	?>
	
	<div class="container mt-3">
	  <h2>Informe o caminho do arquivo</h2>
	  <?php 
	  echo "<form method=post enctype='multipart/form-data' action=manter_detalhesprofessor.php>";
	  ?>
		<div class="mb-3 mt-3">
		  <input type=file name=arquivo>
		</div>
		<button type="submit" name=envarq class="btn btn-primary">Enviar</button>
	  </form>
	</div>
        <?php
                
            $id_p =  $_SESSION['id_prioridade'];
            $id_cat = $_SESSION['id_cat'];
            
            if( $_SESSION['tipo_user'] == "ANALISTA"):
                echo "<a href='editarChamado.php?ID=$id&id_p=$id_p&id_cat=$id_cat' class='btn btn-primary' style=''>Editar CHAMADO</a>";
            endif;
        
        ?>
	</div>
  </div>
</div>







