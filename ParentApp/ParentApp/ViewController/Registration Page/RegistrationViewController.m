//
//  RegistrationViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 02/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "RegistrationViewController.h"
#import "Constants.h"
#import "EmergencyContactViewController.h"
#import "LocalAddressViewController.h"
#import "KidsProfileViewController.h"
#import "BillingAddressViewController.h"
#import "GuardianViewController.h"
#import "HomePageViewController.h"
@interface RegistrationViewController ()

@end

@implementation RegistrationViewController
@synthesize dropDown,dict_loginData;
- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.numFormatter = [[NumberFormatter alloc] initWithRegionCode:@"US"];
    self.view.backgroundColor=kViewBackGroundColor;
    [[self navigationController] setNavigationBarHidden:NO animated:YES];
    self.navigationItem.title = @"Build Profile";
    UILabel *titleLabel = [[UILabel alloc] init];
    titleLabel.text = @"Build Profile";
    [titleLabel setTextColor:[UIColor whiteColor]];
    [titleLabel sizeToFit];
    self.navigationItem.titleView = titleLabel;
    NavigationBarRightButton;
    
    array_gender = [NSArray arrayWithObjects:@"Male",@"Female", nil];
    dict_loginData=[[NSMutableDictionary alloc] init];
    [self setFontTypeAndFontSize];
    self.view.backgroundColor=kViewBackGroundColor;

    
}
-(void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
    [self setFontTypeAndFontSize];

}
-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    contentHight = 0;
    UIView *lLast = view_registration;
    NSInteger wd = lLast.frame.origin.y;
    NSInteger ht = lLast.frame.size.height;
    contentHight = wd+ht+10;
    self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight);
    
}


- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}


#pragma mark Textfield delegate methods
// when editing is started in textfield.
- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string {
    if (textField == txt_phone1 || textField==txt_phone2) {
        textField.text = [self.numFormatter formatText:textField.text];
        return YES;
        
    }
    return YES;
}
- (void)textFieldDidBeginEditing:(UITextField *)textField{
    //DDLogInfo(@"content ht %f",contentHight);

}
- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    if (textField!=txt_confirmPassword){
        return [[self.view viewWithTag:textField.tag+1] becomeFirstResponder];
    }
    else
    {
        [self.backgroundScrollView setContentOffset:CGPointMake(0, 0)];
        DDLogInfo(@"content ht %f btn frm %@",contentHight,NSStringFromCGRect(btn_ChildrenProfile.frame));
        self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight);
        return [textField resignFirstResponder];
    }
}


#pragma mark-UserDefineMethods
-(void)setFontTypeAndFontSize
{

//    btn_otherContacts.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
//    btn_localAddress.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
//    btn_ChildrenProfile.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    
    lbl_FirstName.textColor=kLabelColor;
    lbl_LastName.textColor=kLabelColor;
    lbl_Password.textColor=kLabelColor;
    lbl_Relationship.textColor=kLabelColor;
    lbl_Phone1.textColor=kLabelColor;
    lbl_Phone2.textColor=kLabelColor;
    lbl_Email.textColor=kLabelColor;
    lbl_Password.textColor=kLabelColor;
    lbl_ConfirmPassword.textColor=kLabelColor;
    txt_password.textColor=kColorGrayDark;
    txt_confirmPassword.textColor=kColorGrayDark;

   
}

-(void)saveAction:(UIBarButtonItem *)sender{
    [self.view endEditing:YES];
    if ([self checkUserData]) {
    NSString *str_phone1 = [self.numFormatter rawText:txt_phone1.text];
    NSString *str_phone2 = [self.numFormatter rawText:txt_phone2.text];
    [dict_loginData setSafeObject:txt_firstName.text forKey:kFirstName];
    [dict_loginData setSafeObject:txt_lastName.text forKey:kLastName];
    //[dict_loginData setSafeObject:txt_relationship.text forKey:KRelationship];
    [dict_loginData setSafeObject:str_phone1 forKey:kphone1];
    [dict_loginData setSafeObject:str_phone2 forKey:kphone2];
    [dict_loginData setSafeObject:txt_email.text forKey:kemail];
    [dict_loginData setSafeObject:txt_password.text forKey:kPassword];
    [dict_loginData setSafeObject:txt_confirmPassword.text forKey:kConfirmPassword];
    [dict_loginData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [dict_loginData setSafeObject:[self getDevideIdFromKeyChain] forKey:kDeviceId];
    [self startNetworkActivity:NO];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kRegistration_ParentDetail_API];
    DDLogInfo(@"%@",kRegistration_ParentDetail_API);
    networkManager.delegate = self;
    [networkManager userRegistration:dict_loginData forRequestNumber:1 images:self.array_childProfilePic];

    }
}
-(NSString*)getDevideIdFromKeyChain{
    NSString *bundleId = [[[NSBundle mainBundle] infoDictionary] objectForKey:@"CFBundleIdentifier"];
    KeychainItemWrapper *keychainItem = [[KeychainItemWrapper alloc] initWithIdentifier:bundleId accessGroup:nil];
    NSString *strDeviceId=[keychainItem objectForKey:(__bridge id)(kSecAttrAccount)];
    return strDeviceId;
}
-(BOOL)checkUserData {
    [self.view endEditing:YES];
    BOOL isvalid = YES;
   
    txt_firstName.text = trimedString(txt_firstName.text);
    txt_lastName.text = trimedString(txt_lastName.text);
    //txt_relationship.text = trimedString(txt_relationship.text);
    txt_phone1.text = trimedString(txt_phone1.text);
    txt_phone2.text = trimedString(txt_phone2.text);
    txt_email.text = trimedString(txt_email.text);
    txt_password.text = trimedString(txt_password.text);
    txt_confirmPassword.text = trimedString(txt_confirmPassword.text);
   
    if (txt_firstName==nil|| [txt_firstName.text isEqualToString:@""]) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterFirstName];
        isvalid=NO;
    }
    else if (![[ValidationManager getInstance] isValidName:txt_firstName.text]) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterValidFirstName];
        isvalid=NO;
    }
    else if (txt_lastName.text==nil|| [txt_lastName.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterLastName];
        isvalid = NO;
    }
    else if (![[ValidationManager getInstance] isValidName:txt_lastName.text]) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterValidLastName];
        isvalid=NO;
    }
//    else if (![txt_relationship.text isEqualToString:@""])
//    {
////        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterRelationship];
////        isvalid = NO;
//         if (![[ValidationManager getInstance] isValidName:txt_relationship.text]) {
//            [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterValidRelationship];
//            isvalid=NO;
//        }
//    }
    
    else if (txt_phone1.text==nil|| [txt_phone1.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterMobileNumber];
        isvalid = NO;
    }
    else if ([[self.numFormatter rawText:txt_phone1.text] length]<6)
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterValidMobileNumber];
        isvalid = NO;
    }
    else if (![txt_phone2.text isEqualToString:@""]&&[txt_phone2.text isEqualToString:txt_phone1.text])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kSamePhoneNo];
        isvalid = NO;
    }
    else if (![txt_phone2.text isEqualToString:@""]&&[[self.numFormatter rawText:txt_phone2.text] length]<6)
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterValidMobileNumber];
        isvalid = NO;
    }
    else if (txt_email.text==nil|| [txt_email.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterEmail];
        isvalid = NO;
    }
    else if (![txt_email.text isValidEmail])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterValidEmail];
        isvalid = NO;
    }
    
    else if (txt_password.text==nil|| [txt_password.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:
         kEnterPassword];
        isvalid = NO;
    }
    else if ([txt_password.text length]<6)
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kPasswordLength];
        isvalid = NO;
    }
    
    else if (txt_confirmPassword.text==nil|| [txt_confirmPassword.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterConfrmPassword];
        isvalid = NO;
    }
    else if ([txt_confirmPassword.text length]<6)
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kConfirmPasswordLength];
        isvalid = NO;
        
    }
    else if (![txt_password.text isEqualToString:txt_confirmPassword.text])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kPasswordNotMatch];
        isvalid = NO;
        
    }
    else if ([[dict_loginData safeObjectForKey:kEmergencycontactName]isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterOtherContactDetail];
        isvalid=NO;
    }
    else if ([[dict_loginData safeObjectForKey:kEmergencyPhone]isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterEmergencyContactNumber];
        isvalid=NO;
    }
    else if ([[dict_loginData safeObjectForKey:kLocalAddress]isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterLocalAddress];
        isvalid=NO;
    }
    
    else
    {
        isvalid = YES;
    }
    return isvalid;
    
}
- (IBAction)onClickChooseDOB:(id)sender {
    [self.view endEditing:YES];
    [View_dob setFrame:CGRectMake(0,kiPhone5Position(240), self.view.frame.size.width, View_dob.frame.size.height)];
    birthdayPicker.maximumDate=[NSDate date];
    [self.view addSubview:View_dob];
    
}

- (IBAction)onClickCancleDOB:(id)sender {
    [View_dob setFrame:CGRectMake(0,570, View_dob.frame.size.width, View_dob.frame.size.height)];
    [View_dob removeFromSuperview];
    // [txt_sex becomeFirstResponder];
    
}

- (IBAction)onClickDoneDOB:(id)sender {
    NSDateFormatter *format = [[NSDateFormatter alloc] init];
    NSDate *date = birthdayPicker.date;
    [format setDateFormat:@"dd MMM yyyy"];
    birthdayPicker.maximumDate=[NSDate date];
    [View_dob setFrame:CGRectMake(0,570, View_dob.frame.size.width, View_dob.frame.size.height)];
    birthdayPicker.date=date;
    [View_dob removeFromSuperview];
 

}

- (IBAction)onClickDropDownGender:(id)sender {
    //[self.view endEditing:YES];
    NSArray *array_relation = @[@"Male",@"Female"];
    if(dropDown == nil) {
        CGFloat f = 60;
        dropDown = [[NIDropDown alloc]showDropDown:sender :&f :array_relation :nil :@"down"];
        dropDown.delegate = self;
        
    }
    else {
        [dropDown hideDropDown:sender];
        [self dealloc_dropDown];
    }
    
}
- (IBAction)onClickEmergencyContact:(id)sender {
    EmergencyContactViewController *emergencyContact = [[EmergencyContactViewController alloc]initWithNibName:@"EmergencyContactViewController" bundle:nil];
    [self.navigationController pushViewController:emergencyContact animated:YES];
    
}

- (IBAction)onClickLocalAddress:(id)sender {
    [self.view endEditing:YES];
    LocalAddressViewController *localAddressView = [[LocalAddressViewController alloc]initWithNibName:@"LocalAddressViewController" bundle:nil];
    localAddressView.dict_loginData = [dict_loginData mutableCopy];
    [self.navigationController pushViewController:localAddressView animated:YES];
    
}

- (IBAction)onClickKidsProfile:(id)sender {
    [self.view endEditing:YES];
    KidsProfileViewController *kidsProfile = [[KidsProfileViewController alloc]initWithNibName:@"KidsProfileViewController" bundle:nil];
    kidsProfile.checkValue=3;
    [self.navigationController pushViewController:kidsProfile animated:YES];
    
}

- (IBAction)onClickBillingAddress:(id)sender {
    BillingAddressViewController *billingAddress = [[BillingAddressViewController alloc]initWithNibName:@"BillingAddressViewController" bundle:nil];
    [self.navigationController pushViewController:billingAddress animated:YES];
}

- (IBAction)onClickGuardian:(id)sender {
    [self.view endEditing:YES];
    GuardianViewController *guardianView = [[GuardianViewController alloc]initWithNibName:@"GuardianViewController" bundle:nil];
    guardianView.dict_loginData = [dict_loginData mutableCopy];
    [self.navigationController pushViewController:guardianView animated:YES];
}
-(void)dealloc_dropDown{
    //    [dropDown release];
    dropDown = nil;
}
#pragma mark-NIDropDownDelegate
- (void) niDropDownDelegateMethod: (NIDropDown *) sender {
    [self dealloc_dropDown];
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
                NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
                [userDefaults setObject:dict_responseObj forKey:kDictKeyNSUser];
                [userDefaults synchronize];

                HomePageViewController *homeView = [[HomePageViewController alloc]initWithNibName:@"HomePageViewController" bundle:nil];
                homeView.dict_parentRecord = responseObject;
                [self.navigationController pushViewController:homeView animated:YES];
                
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            
            break;
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    DDLogInfo(@"%@",error);
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}
@end
