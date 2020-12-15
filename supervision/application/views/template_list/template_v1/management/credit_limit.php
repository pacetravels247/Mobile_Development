<form action="<?php echo base_url() . 'management/credit_balance_update?domain_origin='.$user_details['Bid'] ?>" method="POST">
    <table class="table table-condensed">                   
        <thead>                  
            <tr>                 
                <th>Agent Name</th>     
                <td>                  
                    <?php echo $user_details['agency_name']; ?>                                                    
                </td>                 
            </tr>  
            <tr>                 
                <th>Agent Id</th>     
                <td>                  

                    <?php echo provab_decrypt($user_details['uuid']); ?>                                            
                </td>                 
            </tr>  
            <tr>                    
                <th>Balance</th>        
                <td> <?php echo $user_details['balance']; ?>  </td>                  
            </tr>
            <tr>                    
                <th>Available Balance</th>        
                <td> <?=$user_details['balance']+$user_details['credit_limit']+$user_details['due_amount']?>  </td>                  
            </tr>                       
            <tr>                         
                <th>Due Amount</th>        
                <td> <?php echo $user_details['due_amount']; ?>  </td>     
            </tr>                      
            <tr>                  
                <th>Credit Limit</th>     
                <td><input type="number" min="0" name="amount" id="amount" value="<?php echo $user_details['credit_limit']; ?>" required=""></td>       
            </tr>                      
            <tr>                      
                <th>&nbsp;</th>        
                <td> <input type="hidden" name="user_id" value="<?php echo $user_details['user_id']; ?>">
                    <input type="hidden" name="origin" value="<?php echo $user_details['Bid']; ?>">
                    <input type="submit" value="Update" class="btn-sm btn-primary"></td>     
            </tr>                                
        </thead>                
    </table>
</form>        