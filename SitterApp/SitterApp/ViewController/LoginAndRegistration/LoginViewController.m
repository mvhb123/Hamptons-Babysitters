//
//  LoginViewController.m
//  SitterApp
//
//  Created by Vikram gour on 07/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "LoginViewController.h"
#import "RegistrationViewController.h"
@interface LoginViewController ()

@end

@implementation LoginViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
//    if (![[NSUserDefaults standardUserDefaults] objectForKey:kUserStateZone]) {
//        UIAlertView *alert=[[UIAlertView alloc] initWithTitle:@"" message:@"Please select your state"  delegate:self cancelButtonTitle:@"New York" otherButtonTitles:@"California", nil];
//        [alert setTag:101];
//        [alert show];
//    }
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    [self.navigationController.navigationBar setHidden:YES];
    [self.view setBackgroundColor:kBackgroundColor];
    txt_userName.text=@"";
    txt_password.text=@"";
    [txt_userName setTextColor:kColorGrayDark];
    [txt_password setTextColor:kColorGrayDark];

    [btn_login.titleLabel setFont:[UIFont fontWithName:Roboto_Light size:FontSize16]];
    [btn_signUp.titleLabel setFont:[UIFont fontWithName:Roboto_Regular size:FontSize13]];
    [btn_forgotPassword.titleLabel setFont:[UIFont fontWithName:Roboto_Regular size:FontSize13]];
    [btn_forgotPassword setTitleColor:UIColorFromHexCode(0x04005c) forState:UIControlStateNormal];
    [btn_signUp setTitleColor:UIColorFromHexCode(0x04005c) forState:UIControlStateNormal];

    [self.backgroundScrollView setBackgroundColor:kBackgroundColor];

}
/*
 #pragma mark - Navigation
 
 // In a storyboard-based application, you will often want to do a little preparation before navigation
 - (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
 // Get the new view controller using [segue destinationViewController].
 // Pass the selected object to the new view controller.
 }
 */
#pragma mark- UITextFieldDelegate
- (void)textFieldDidBeginEditing:(UITextField *)textField
{
    
}

- (BOOL)textFieldShouldReturn:(UITextField *)textField
{
    if (textField == txt_userName) {
        [txt_password becomeFirstResponder];
    }else if (textField == txt_password) {
        [textField resignFirstResponder];
    }else
        [textField resignFirstResponder];
    return true;
}

#pragma mark- UserDefinedMethods
/**
 On clicking this button user can be able to login into app
 @param id A sender object which holds the UIButton object which initiated this action
 @return void
 */
- (IBAction)onClicked_Login:(id)sender {
    [txt_password resignFirstResponder];
    [txt_userName resignFirstResponder];
    //AppDelegate *appDelegate=kAppDelegate;
    //[appDelegate setRootViewAfterLogin];
    
    if([self checkData])
    {
        NSMutableDictionary *dict_loginData=[[NSMutableDictionary alloc] init];
        [dict_loginData setSafeObject:txt_userName.text forKey:kUserName];
        [dict_loginData setSafeObject:txt_password.text forKey:kPassword];
        [dict_loginData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
        if([[NSUserDefaults standardUserDefaults] objectForKey:DEVICE_TOKEN]){
            [dict_loginData setSafeObject:[[NSUserDefaults standardUserDefaults]objectForKey:DEVICE_TOKEN] forKey:DEVICE_TOKEN];
        }
        [dict_loginData setSafeObject:[self getDevideIdFromKeyChain] forKey:kDeviceId];

        [self startNetworkActivity:NO];
        SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kLOGIN_API];
        networkManager.delegate = self;
        [networkManager loginUser:dict_loginData forRequestNumber:1];
    }
}

/**
 On clicking this button user can be able to registered for app
 @param id A sender object which holds the UIButton object which initiated this action
 @return void
 */
- (IBAction)onClicked_signUp:(id)sender {
   // [[UIApplication sharedApplication] openURL:[NSURL URLWithString:kSitterRegistrationUrl]];
}

/**
 On clicking this button user can be able to get new forword through valid email.
 @param id A sender object which holds the UIButton object which initiated this action
 @return void
 */
- (IBAction)onClicked_forgotPassword:(id)sender {
    [self.view endEditing:YES];
    UIAlertView *emailAlert=[[UIAlertView alloc] initWithTitle:@"Forgot Password" message:@"" delegate:self cancelButtonTitle:@"Cancel" otherButtonTitles:@"OK", nil];
    emailAlert.tag=100;
    emailAlert.alertViewStyle = UIAlertViewStylePlainTextInput;
    UITextField *theTextField =  [emailAlert textFieldAtIndex:0];
    theTextField.keyboardType = UIKeyboardTypeEmailAddress;
    theTextField.text=@"";
    theTextField.placeholder=kEnterEmail;
    theTextField.layer.cornerRadius=5.0;
    theTextField.clipsToBounds=YES;
    [emailAlert show];
}

/**
 Method is used to chech the validation for data.
 @param nil
 @return Bool
 */
-(BOOL) checkData{
    if([txt_userName.text length]==0){
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEnterEmail];
        return NO;
    }
    else if ([txt_userName.text length]>0 && ![txt_userName.text isValidEmail])//[app isValidEmailId:txt_userName.text])
    {
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:kEnterValidEmail];
        return NO;
    }
    else if([txt_password.text length]==0){
        [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEnterPassword];
        return NO;
    }/*else if(![txt_userName.text isValidEmail]){
      [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEnterValidEmail];
      return NO;
      }*/
    return YES;
}
-(NSString*)getDevideIdFromKeyChain{
    NSString *bundleId = [[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleIdentifier"];
    KeychainItemWrapper *keychainItem = [[KeychainItemWrapper alloc] initWithIdentifier:bundleId accessGroup:nil];
    NSString *strDeviceId=[keychainItem objectForKey:(__bridge id)(kSecAttrAccount)];
    return strDeviceId;
}

#pragma mark - SMCoreNetworkManagerDelegate

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSMutableDictionary *dict_responseObj=[[NSMutableDictionary alloc] init];
    [dict_responseObj addEntriesFromDictionary:responseObject];
    [self stopNetworkActivity];
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [[ApplicationManager getInstance] saveLogInData:dict_responseObj];
                DDLogInfo(@"user data %@",dict_responseObj);
                [[NSUserDefaults standardUserDefaults]setObject:dict_responseObj  forKey:kLogedinUserDetail];
                [[NSUserDefaults standardUserDefaults] synchronize];
                AppDelegate *appDelegate=kAppDelegate;
                [UIApplication sharedApplication].applicationIconBadgeNumber = [[[dict_responseObj safeObjectForKey:kData ] safeObjectForKey:@"notification_count"] integerValue];
                [appDelegate setRootViewAfterLogin];
                [appDelegate reSetNotificationCount];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            
            break;
        case 2:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kMessage]];
            }else{
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            break;
        default:
            break;
            
    }
}

- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}
#pragma mark -- AlertView delegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    NSString *email;
    if (alertView.tag==100 && buttonIndex==1) {
        UITextField *theTextField =  [alertView textFieldAtIndex:0];
        email=theTextField.text;
        if([email length]!=0 ){
            if ([email isValidEmail])
            {
                DDLogInfo(@"%@",email);
                NSMutableDictionary *dict_forgotPasswordData=[[NSMutableDictionary alloc] init];
                [dict_forgotPasswordData setSafeObject:email forKey:kEmail];
                [dict_forgotPasswordData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
                [dict_forgotPasswordData setSafeObject:@"S" forKey:@"usertype"];
                [self startNetworkActivity:NO];
                SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kFORGOT_PASSWORD_API];
                networkManager.delegate = self;
                [networkManager forgotPassword:dict_forgotPasswordData forRequestNumber:2];
            }
            else
            {
                UIAlertView *alert=[[UIAlertView alloc] initWithTitle:@"" message:kEnterValidEmail delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
                alert.tag=103;
                [alert show];
            }
        }
        else{
            [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEnterEmail];
        }
    }
    else if (alertView.tag==101) {// for user state zone
      NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
            if (buttonIndex==1) {
                [userDefaults setObject:@"California" forKey:kUserStateZone];
            }
            else
            {
                [userDefaults setObject:@"New York" forKey:kUserStateZone];
            }
        
            [userDefaults synchronize];
    }
    else if (alertView.tag==103){
        [btn_forgotPassword sendActionsForControlEvents:UIControlEventTouchUpInside];
    }
    
    [self appDidLogout:alertView clickedButtonAtIndex:buttonIndex];

}

@end
