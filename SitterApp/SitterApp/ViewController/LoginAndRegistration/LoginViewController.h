//
//  LoginViewController.h
//  SitterApp
//
//  Created by Vikram gour on 07/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"

@interface LoginViewController : AppBaseViewController
{

    __weak IBOutlet UITextField *txt_userName;
    __weak IBOutlet UITextField *txt_password;
    __weak IBOutlet UIButton *btn_login;
    __weak IBOutlet UIButton *btn_signUp;
    __weak IBOutlet UIButton *btn_forgotPassword;
}
- (IBAction)onClicked_Login:(id)sender;
- (IBAction)onClicked_signUp:(id)sender;
- (IBAction)onClicked_forgotPassword:(id)sender;
@end
