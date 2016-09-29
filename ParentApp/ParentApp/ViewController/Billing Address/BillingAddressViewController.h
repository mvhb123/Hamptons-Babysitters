//
//  BillingAddressViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 08/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"

@interface BillingAddressViewController : AppBaseViewController
{
    __weak IBOutlet UITextField *txt_streetAddress;
    __weak IBOutlet UITextField *txt_zip;
    __weak IBOutlet UITextField *txt_country;
    __weak IBOutlet UITextField *txt_city;
    __weak IBOutlet UITextField *txt_state;
}

@end
