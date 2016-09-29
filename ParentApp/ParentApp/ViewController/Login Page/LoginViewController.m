//
//  LoginViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 02/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "LoginViewController.h"
#import "RegistrationViewController.h"
#import "HomePageViewController.h"
#import "Constants.h"
#import "APIURL.h"
#import "ApplicationManager.h"
#import "SMUITextField.h"

@interface LoginViewController ()

@end

@implementation LoginViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    [[self navigationController] setNavigationBarHidden:YES animated:YES];
    [self.view setBackgroundColor:kViewBackGroundColor];
    array_stateList = [[NSArray alloc] initWithObjects:@"New york",@"Calefornia",nil];
    
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
   
}
-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    [[self navigationController] setNavigationBarHidden:YES animated:YES];
    txt_password.textColor=kColorGrayDark;
    txt_userName.textColor=kColorGrayDark;
    AppDelegate *appDel=kAppDelegate;
    [appDel setApplicationTheme];
    NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
    dict_autoLogin = [defaults objectForKey:kDictKeyNSUser];
    
    if (dict_autoLogin !=nil) {
        [self callAutoLogin];
    }
    else
    {
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        NSString *str_state = [defaults objectForKey:kstateKey];
        str_sateValue = kNewYork;
        
        NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
        [userDefaults setObject:str_sateValue forKey:kstateKey];
        [userDefaults synchronize];
//        if (str_state==nil || [str_state isEqualToString:@""]) {
//            //[self callPickerView];
//            UIAlertView *alert=[[UIAlertView alloc] initWithTitle:@"" message:@"Please choose your state." delegate:self cancelButtonTitle:@"New York" otherButtonTitles:@"California", nil];
//            [alert setTag:1003];
//            [alert show];
//        }
     }

    txt_userName.text = @"";
    txt_password.text = @"";
}
-(void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    [self.backgroundScrollView setContentSize:CGSizeMake(self.view.frame.size.width, self.backgroundView.frame.size.height)];
    [self setFontTypeAndFontSize];
    
}
-(void)callAutoLogin
{
    [[ApplicationManager getInstance] saveLogInData:dict_autoLogin];
    HomePageViewController *homeView = [[HomePageViewController alloc]initWithNibName:@"HomePageViewController" bundle:nil];
    [self.navigationController pushViewController:homeView animated:NO];
}
-(void)callPickerView
{
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.5];
    [UIView setAnimationDelegate:self];
    [view_picker setFrame:CGRectMake(0,0,view_picker.frame.size.width,view_picker.frame.size.height)];
    [self.view addSubview:view_picker];
    [UIView commitAnimations];

}

#pragma mark-UITextFieldDelegates

- (void)textFieldDidBeginEditing:(UITextField *)textField
{
    //[self.backgroundScrollView setContentSize:CGSizeMake(self.view.frame.size.width,btn_SignUp.frame.size.height+btn_SignUp.frame.origin.y+216)];
}
- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    [self.view endEditing:YES];
    if (textField!=txt_password){
        [[self.view viewWithTag:textField.tag+1] becomeFirstResponder];
    }
    else
    {
        [self.backgroundScrollView setContentOffset:CGPointMake(0, 0)];
        [self.backgroundScrollView setContentSize:CGSizeMake(self.view.frame.size.width, self.backgroundView.frame.size.height)];
    }
    
    return [textField resignFirstResponder];
}

#pragma mark-UserDefineMethods
-(void)setFontTypeAndFontSize
 {
     [txt_userName setFont:[UIFont fontWithName:RobotoRegularFont size:12.0]];
     [txt_password setFont:[UIFont fontWithName:RobotoRegularFont size:12.0]];
     [btn_login.titleLabel setFont:[UIFont fontWithName:RobotoLightfont size:16.0]];
     [btn_login setTitleColor:UIColorFromHexCode(0xffffff) forState:UIControlStateNormal];
     [btn_SignUp setTitleColor:UIColorFromHexCode(0x263068) forState:UIControlStateNormal];
     [btn_ForgotPwd setTitleColor:UIColorFromHexCode(0x263068) forState:UIControlStateNormal];

}

- (void)onTouchOnBackground:(UITapGestureRecognizer*)sender {
    [self.view endEditing:YES];
    [self.backgroundScrollView setContentOffset:CGPointMake(0, 0)];
    [self.backgroundScrollView setContentSize:CGSizeMake(self.view.frame.size.width, self.view.frame.size.height)];
    
}
// Method for load registration page.
- (IBAction)onClickSignUp:(id)sender {
    RegistrationViewController *registrationView = [[RegistrationViewController alloc]initWithNibName:@"RegistrationViewController" bundle:nil];
    [self.navigationController pushViewController:registrationView animated:YES];
}
// Method for show alert forgot password.
- (IBAction)onClickForgotPassword:(id)sender {
    [self.view endEditing:YES];
    UIAlertView *emailAlert=[[UIAlertView alloc] initWithTitle:@"Forgot Password" message:@"" delegate:self cancelButtonTitle:@"Cancel" otherButtonTitles:@"OK", nil];
    emailAlert.tag=100;
    emailAlert.alertViewStyle = UIAlertViewStylePlainTextInput;
    UITextField *theTextField = [emailAlert textFieldAtIndex:0];
    theTextField.keyboardType = UIKeyboardTypeEmailAddress;
    theTextField.returnKeyType = UIReturnKeyDone;
    theTextField.text=@"";
    theTextField.placeholder=kEnterEmail;
    theTextField.clipsToBounds=NO;
    [emailAlert show];
    NSLog(@"class %@",[theTextField superclass]);
}
// Method for call login API on click login button.
- (IBAction)onClickLogin:(id)sender {
    [self.view endEditing:YES];
    if ([self checkUserData]) {
        NSMutableDictionary *dict_loginData=[[NSMutableDictionary alloc] init];
        [dict_loginData setSafeObject:txt_userName.text forKey:kUserName];
        [dict_loginData setSafeObject:txt_password.text forKey:kPassword];
        [dict_loginData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
        [dict_loginData setSafeObject:[self getDevideIdFromKeyChain] forKey:kDeviceId];
        [self startNetworkActivity:NO];
        SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kLOGIN_API];
        DDLogInfo(@"%@",kLOGIN_API);
        networkManager.delegate = self;
        [networkManager loginUser:dict_loginData forRequestNumber:1];
    }
}

- (IBAction)onClickDoneState:(id)sender {
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    [userDefaults setObject:str_sateValue forKey:kstateKey];
    [userDefaults synchronize];
    [view_picker removeFromSuperview];
}
-(NSString*)getDevideIdFromKeyChain{
    NSString *bundleId = [[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleIdentifier"];
    KeychainItemWrapper *keychainItem = [[KeychainItemWrapper alloc] initWithIdentifier:bundleId accessGroup:nil];
    NSString *strDeviceId=[keychainItem objectForKey:(__bridge id)(kSecAttrAccount)];
    return strDeviceId;
}
// Method for check validation for login profile.
-(BOOL)checkUserData
{
    BOOL isvalid;
    isvalid = NO;
    txt_userName.text = trimedString(txt_userName.text);
    txt_password.text = trimedString(txt_password.text);
    if (txt_userName.text==nil|| [txt_userName.text isEqualToString:@""]) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterEmail];
        isvalid=NO;
    }
    else if (![txt_userName.text isValidEmail])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterValidEmail];
        isvalid = NO;
    }
    
    else if (txt_password.text==nil|| [txt_password.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterPassword];
        isvalid = NO;
    }
    else if ([txt_password.text length]<6)
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kPasswordLength];
        isvalid = NO;
        
    }
    else
    {
        isvalid = YES;
    }
    return isvalid;
}
#pragma mark -- Picker View delegates
// Number of components.
-(NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView{
    return 1;
}
// Total rows in our component.
-(NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component{
    return [array_stateList count];
}
// Display each row's data.
-(NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component{

    return [array_stateList objectAtIndex:row];
}

// Do something with the selected row.
-(void)pickerView:(UIPickerView *)pickerView didSelectRow:(NSInteger)row inComponent:(NSInteger)component{
    str_sateValue = [array_stateList objectAtIndex:row];
}

#pragma mark - SMCoreNetworkManagerDelegate
- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    DDLogInfo(@"%@",responseObject);
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                
                [[ApplicationManager getInstance] saveLogInData:dict_responseObj];
                [UIApplication sharedApplication].applicationIconBadgeNumber = [[[dict_responseObj safeObjectForKey:kData ] safeObjectForKey:@"notification_count"] integerValue];
                NSString *strProfileStatus=[[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:kUserDetail]  safeObjectForKey:kProfileStatus];
                NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
                [userDefaults setObject:dict_responseObj forKey:kDictKeyNSUser];
                [userDefaults synchronize];
                
                if ([strProfileStatus isEqualToString:@"1"]) {//For user profile is complete
                    HomePageViewController *homeView = [[HomePageViewController alloc]initWithNibName:@"HomePageViewController" bundle:nil];
                    [self.navigationController pushViewController:homeView animated:YES];
                }else{
                    HomePageViewController *homeView = [[HomePageViewController alloc]initWithNibName:@"HomePageViewController" bundle:nil];
                    [self.navigationController pushViewController:homeView animated:NO];
                }
                
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            
            break;
        case 2:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[dict_responseObj valueForKey:kMessage]];
            }else{
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            break;
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}
#pragma mark -- AlertViewDelegate
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
                [dict_forgotPasswordData setObject:@"P" forKey:kUserType];
                [self startNetworkActivity:NO];
                SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kFORGOT_PASSWORD_API];
                networkManager.delegate = self;
                
                [networkManager forgotPassword:dict_forgotPasswordData forRequestNumber:2];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:kEnterEmail];
            }
        }
        else{
            [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:kEnterEmail];
        }
    }
    if (alertView.tag == 1003) {
        if (buttonIndex==1) {
            str_sateValue = kCalifornia;
        }
        else
            str_sateValue = kNewYork;
        
        NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
        [userDefaults setObject:str_sateValue forKey:kstateKey];
        [userDefaults synchronize];
    }
}
@end
