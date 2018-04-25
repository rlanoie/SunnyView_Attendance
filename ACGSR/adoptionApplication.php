<!DOCTYPE html>

<html lang="en">
  <head>
    <title>ACGSR Adoption</title>
    	<!-- Meta-Tags -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="keywords" content="ACGSRescue, Animal Rescue, Animal Adoption, Pet Adoption, TNR, Non-Profit, Felines, Cats, Dogs, Canines">
    <link rel="stylesheet" type="text/css" href="ACGSRescue.css" />
  </head>
  
  <body>
    <header>
      <h1>ACGSRescue</h1>
      
    </header>
    <section id = "body">
      <div class = "container">
        <div id = "page">
          <header>
            <h2>Adoption Application</h2>
          </header>
          <div id="pageInstructions" class="instructions">
            <div class="instructionsContainer">
              <p>Please provide the following information to assist us in matching the right pet to the right home.  In order to process the adoption, this form must be completed, reviewed and then approved.
              <br><br>Adoption Fee for Felines is $135 and the adoption fee for Canines is $275.  The adoption fee includes Veterinary Health Examination, Vaccinations, Deworming, HeartWorm/ FEL/FIV Testing, Microchip, and Advantage/Frontline. </p>              
            </div>
          </div>        
          <div id = "formDetails">
            <form id="adoptionForm">
              <fieldset class="customerContact">
                <div class="formRow-error">   
                </div>
                <div class="formRow">
                  <div class = "col-md-1">
                    <label for="firstName" hidden>First Name</label>
                    <input type="text" name="firstName" id="firstName" value="First Name">  
                  </div>
                </div>
                <div class="formRow">  
                  <div class = "col-md-4">
                    <label for="middleInitial" hidden>Middle Initial</label>
                    <input type="text" name="middleInitial" id="middleInitial" value="Initial">  
                  </div>
                  <div class = "col-md-75">
                    <label for="lastName" hidden>Last Name</label>
                    <input type="text" name="lastName" id="lastName" value="Last Name">  
                  </div>
               </div>
                <div class="formRow">
                 <div class = "col-md-1">
                   <label for="email" hidden>Email</label>
                   <input type="email" name="email" id="email" value="Email">  
                 </div>
               </div> 
                <div class="formRow">
                 <div class = "col-md-1">
                   <label for="newPassword" hidden>Password</label>
                   <input type="password" name="newPassword" id="newPassword"  autocomplete="newPassword">  
                 </div>
               </div>
                <div class="formRow">
                 <div class = "col-md-1">
                   <label for="confirmPassword" hidden>Confirm Your Password</label>
                   <input type="password" name="confirmPassword" id="confirmPassword" autocomplete="newPassword">  
                 </div>
               </div>
                <div class="formRow">
                  <div class="col-md-1 centerText">
                    <button type="submit">Next</button>
                  </div>
                </div>
              </fieldset>  
              <fieldset hidden>  
               <div class="formRow-error">   
                </div>
               <div class="formRow">
                  <div class = "col-md-40">
                    <label for="streetAddress" hidden>Street Address</label>
                    <input type="text" name="streetAddress" id="streetAddress" value="Street Address">  
                  </div>
                  <div class = "col-md-5">
                    <label for="unitNumber" hidden>Unit Number</label>
                    <input type="text" name="unitNumber" id="unitNumber" value="Unit #">  
                  </div>
                  <div class = "col-md-40">
                    <label for="city" hidden>City</label>
                    <input type="text" name="city" id="city" value="City">  
                  </div>
                  <div class = "col-md-40">
                    <label for="state" hidden>State</label>
                    <input type="text" name="state" id="state" value="State">  
                  </div>
                  <div class = "col-md-40">
                    <label for="zipcode" hidden>Zip Code</label>
                    <input type="text" name="zipcode" id="zipcode" value="Zip Code">  
                  </div>
                </div>
              </fieldset>  
            </form>
          </div>     
        </div>
      </div>
    </section>
  </body>
  
</html>