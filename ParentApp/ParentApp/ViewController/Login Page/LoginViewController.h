//
//  LoginViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 06/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "SMCoreNetworkFramework.h"
#import "EditProfileViewController.h"



@interface LoginViewController : AppBaseViewController<SMCoreNetworkManagerDelegate>
{
    IBOutlet UIView *view_picker;
    __weak IBOutlet UIBarButtonItem *btn_doneState;
    __weak IBOutlet UIPickerView *pickerView;
    __weak IBOutlet UIButton *btn_login;
    __weak IBOutlet UITextField *txt_userName;
    __weak IBOutlet UITextField *txt_password;
    __weak IBOutlet UIButton *btn_SignUp;
    __weak IBOutlet UIButton *btn_ForgotPwd;
    NSDictionary *dict_autoLogin;
    NSArray *array_stateList;
    NSString *str_sateValue;
}
- (IBAction)onClickLogin:(id)sender;
- (IBAction)onClickDoneState:(id)sender;

@end
