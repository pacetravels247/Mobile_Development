<style>
   .a-server {
      width: 60%;
      margin: 1em auto;
      text-align: center;
      border: .5em solid gray;
      padding: 1em;
   }
   .a-server .p-err-sorry {
      color: gray;
   }
   .a-server .p-err {
      color: indianred;
   }
   .a-server .center-responsive {
      width: 30%;
      margin: 1em auto;
   }
   .a-server nav {
      margin: 1em auto;
   }
   .a-server ul {
      list-style: none;
      margin: 0;
      padding: 0;
   }
   .a-server ul li {
      display: inline;
   }
   .a-server .btn-as {
      padding: 1em 3em;
      margin: 1em .3em;
      color: white;
      border: 0;
      outline: 0;
   }
   .btn-lime {
      background: #98c000;
   }
   .btn-red {
      background: #ea2e49;
   }
   .btn-gray {
      background: #3d4c53;
   }
   .btn-cyan {
      background: #0cdbe8;
   }
</style>


<div class="a-server">
   <img src="<?php echo $GLOBALS['CI']->template->template_images('404.jpg'); ?>" alt="sorry" class="center-responsive">
   <p class="p-err-sorry">Ooooooops!!!!!!!!!
      </p>
         <p class="p-err">Seems like the url which you are trying to access is not found on our system !!!</p>
         <nav>
            <ul>
               <li>
                  <a href='<?php echo base_url(); ?>general/'><span><button class="btn-as btn-lime">Find Dashboard</button></span></a>
               </li>
               <li>
                  <a href='<?php echo base_url(); ?>general/'><span><button class="btn-as btn-red">Find Dashboard</button></span></a>
               </li>
               <li>
                  <a href='<?php echo base_url(); ?>general/'><span><button class="btn-as btn-gray">Find Dashboard</button></span></a>
               </li>
               <li>
                  <a href='<?php echo base_url(); ?>general/'><span><button class="btn-as btn-cyan">Find Dashboard</button></span></a>
               </li>
            </ul>
         </nav>
</div>


