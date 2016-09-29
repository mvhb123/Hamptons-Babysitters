//
//  EditProfileViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "EditProfileViewController.h"
#import "GuardianViewController.h"
#import "LocalAddressViewController.h"
#import "KidsProfileViewController.h"
#import "ViewKidsProfileViewController.h"
#import "MyProfileViewController.h"
#import "PaymentViewController.h"
#import "BookingCreditsViewController.h"

@interface EditProfileViewController ()

@end

@implementation EditProfileViewController
@synthesize dict_userProfile,dict_updateProfileData;
- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib
    self.numFormatter = [[NumberFormatter alloc] initWithRegionCode:@"US"];
    dict_updateProfileData = [[NSMutableDictionary alloc]init];
    self.navigationItem.title = @"Edit Profile";
    NavigationBarRightButton;
    
    self.parentInfo = [ApplicationManager getInstance].parentInfo;
    [self addDataInFields];
    self.view.backgroundColor=kViewBackGroundColor;
   [self setFontTypeAndFontSize];
    if (![self.parentInfo.profileStatus isEqualToString:@"1"]) {
        [self addLogoutButtonForNavigationBar];
    }
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:YES];
    DDLogInfo(@"dict is %@", dict_updateProfileData);
}
-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    contentHight = 0;
    UIView *lLast = [view_editProfile.subviews lastObject];
    NSInteger wd = lLast.frame.origin.y;
    NSInteger ht = lLast.frame.size.height;
    contentHight = wd+ht;
    self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,500);
    
}
- (void)onTouchOnBackground:(UITapGestureRecognizer*)sender {
    [self.view endEditing:YES];
    [self.backgroundScrollView setContentOffset:CGPointMake(0, 0)];
    self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,500);
}
- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string {
    if (textField == txt_phone1 || textField==txt_phone2) {
        textField.text = [self.numFormatter formatText:textField.text];
        return YES;
    }
    return YES;
}
- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    [self.view endEditing:YES];
    
    if (textField!=txt_email){
        [[self.view viewWithTag:textField.tag+1] becomeFirstResponder];
    }
    else
    {
        self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+20);
    }
    return [textField resignFirstResponder];
}
-(void)saveAction:(UIBarButtonItem *)sender{
    [self.view endEditing:YES];
    if ([self checkUserData]) {
        NSString *str_phone1 = [self.numFormatter rawText:txt_phone1.text];
        NSString *str_phone2 = [self.numFormatter rawText:txt_phone2.text];
        
        NSMutableDictionary *dict_localAddress = [[NSMutableDictionary alloc]init];
        [dict_updateProfileData setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
        [dict_updateProfileData setSafeObject:self.parentInfo.tokenData forKey:kToken];
        [dict_updateProfileData setSafeObject:txt_firstName.text forKey:kFirstName];
        [dict_updateProfileData setSafeObject:txt_lastName.text forKey:kLastName];
        [dict_updateProfileData setSafeObject:txt_relationship.text forKey:KRelationship];
        [dict_updateProfileData setSafeObject:str_phone1 forKey:kphone1];
        [dict_updateProfileData setSafeObject:str_phone2 forKey:kphone2];
        [dict_updateProfileData setSafeObject:txt_email.text forKey:kemail];
        [dict_updateProfileData setSafeObject:self.parentInfo.AddressID forKey:kAddressId];
        [dict_updateProfileData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
        if ([dict_updateProfileData objectForKey:kLocalAddress]) {
            
        }
        else
        {
            [dict_localAddress setSafeObject:self.parentInfo.HotelName forKey:kHotelName];
            [dict_localAddress setSafeObject:self.parentInfo.CrossStreet forKey:kCrossStreet];
            [dict_localAddress setSafeObject:self.parentInfo.StreetAddress forKey:kStreetAddress];
            [dict_localAddress setSafeObject:self.parentInfo.City forKey:kCity];
            [dict_localAddress setSafeObject:self.parentInfo.stateID forKey:kState];
            [dict_localAddress setSafeObject:self.parentInfo.zipCode forKey:kZip];
            [dict_localAddress setSafeObject:self.parentInfo.addressType forKey:kAddressType];
            [dict_localAddress setSafeObject:self.parentInfo.AddressID forKey:kAddressId];
            NSData *data = [NSJSONSerialization dataWithJSONObject:dict_localAddress options:NSJSONWritingPrettyPrinted error:nil];
            NSString *str_localAddress = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
            [dict_updateProfileData setSafeObject:str_localAddress forKey:kLocalAddress];
            
        }
        if (self.chaeckData != 2) {
            [dict_updateProfileData setSafeObject:self.parentInfo.parentGurdianName forKey:kSpouseFirstName];
            [dict_updateProfileData setSafeObject:self.parentInfo.parentGurdianRelationship forKey:kSpouseRelation];
            [dict_updateProfileData setSafeObject:self.parentInfo.parentGurdianPhone1 forKey:kSpousePhone1];
            [dict_updateProfileData setSafeObject:self.parentInfo.parentGurdianPhone2 forKey:kSpousePhone2];
            [dict_updateProfileData setSafeObject:self.parentInfo.parentEmergencyContactName forKey:kEmergencycontactName];
            [dict_updateProfileData setSafeObject:self.parentInfo.parentEmergencyRelation forKey:kEmergencyRelation];
            [dict_updateProfileData setSafeObject:self.parentInfo.parentEmergencyPhone forKey:kEmergencyPhone];
        }
        
        [self startNetworkActivity:NO];
        SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kUpdateUserProfile];
        DDLogInfo(@"%@",kUpdateUserProfile);
        networkManager.delegate = self;
        [networkManager updateUserProfile:dict_updateProfileData forRequestNumber:1];
    }
}
#pragma mark-UserDefineMethods
-(void)setFontTypeAndFontSize
{
    // for label fonts
    lbl_firstName.font=[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_lastName.font=[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_relationship.font=[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_phone1.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_phone2.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_email.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
   
    // for button fonts
    btn_guardian.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    btn_jobAddress.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    btn_childrenProfile.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    btn_bookingCredits.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    btn_creditCardDetail.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];

    [lbl_firstName setTextColor:kLabelColor];
    [lbl_lastName setTextColor:kLabelColor];
    [lbl_relationship setTextColor:kLabelColor];
    [lbl_phone1 setTextColor:kLabelColor];
    [lbl_phone2 setTextColor:kLabelColor];
    [lbl_email setTextColor:kLabelColor];
}

-(BOOL)checkUserData {
    [self.view endEditing:YES];
    BOOL isvalid = YES;
    txt_firstName.text = trimedString(txt_firstName.text);
    txt_lastName.text = trimedString(txt_lastName.text);
    txt_relationship.text = trimedString(txt_relationship.text);
    txt_phone1.text = trimedString(txt_phone1.text);
    txt_phone2.text = trimedString(txt_phone2.text);
    txt_email.text = trimedString(txt_email.text);
    if (txt_firstName==nil|| [txt_firstName.text isEqualToString:@""]) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterFirstName];
        isvalid=NO;
    }
    else if (txt_lastName.text==nil|| [txt_lastName.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterLastName];
        isvalid = NO;
    }
//    else if (txt_relationship.text==nil|| [txt_relationship.text isEqualToString:@""])
//    {
//        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterRelationship];
//        isvalid = NO;
//    }
    
    else if (txt_phone1.text==nil|| [txt_phone1.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterMobileNumber];
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
    else
    {
        isvalid = YES;
    }
    return isvalid;
}

- (IBAction)onClickEditGurdian:(id)sender {
    [self.view endEditing:YES];
    GuardianViewController *gurdianView = [[GuardianViewController alloc]initWithNibName:@"GuardianViewController" bundle:nil];
    gurdianView.checkValue = 1;
    if ([[dict_updateProfileData allKeys]containsObject:kEmergencycontactName]) {
        gurdianView.dict_savedUpdatedData = [dict_updateProfileData mutableCopy];
    }
    else
        gurdianView.CheckFirstTimeEdit = 1;
    [self.navigationController pushViewController:gurdianView animated:YES];
}

- (IBAction)onClickJobAddress:(id)sender {
    [self.view endEditing:YES];
    LocalAddressViewController *jobAddress = [[LocalAddressViewController alloc]initWithNibName:@"LocalAddressViewController" bundle:nil];

    if ([[dict_updateProfileData allKeys]containsObject:kLocalAddress]) {
        jobAddress.dict_savedLocalData = [dict_updateProfileData mutableCopy];
    }
    else
        jobAddress.dict_profileData = [self.parentInfo.dict_parentLocalAddress mutableCopy];
    [self.navigationController pushViewController:jobAddress animated:YES];
}

- (IBAction)onClickEditChildrenProfile:(id)sender {
    
    if ([self.parentInfo.parentChildCountName integerValue]== 0) {
        UIAlertView *emailAlert=[[UIAlertView alloc] initWithTitle:@"" message:kchildNotAdded delegate:self cancelButtonTitle:@"No" otherButtonTitles:@"Yes", nil];
        emailAlert.tag=100;
        [emailAlert show];
        
    }
    else
    {
        ViewKidsProfileViewController *viewkidsProfile = [[ViewKidsProfileViewController alloc]initWithNibName:@"ViewKidsProfileViewController" bundle:nil];
        viewkidsProfile.dict_childRecord = [dict_userProfile mutableCopy];
        [self.navigationController pushViewController:viewkidsProfile animated:YES];
        
    }
    
    
}

- (IBAction)onClickCreditCardDetail:(id)sender {
    PaymentViewController *paymentView = [[PaymentViewController alloc]initWithNibName:@"PaymentViewController" bundle:nil];
    paymentView.CheckValue = 1;
    [self.navigationController pushViewController:paymentView animated:YES];
}

- (IBAction)onClickBookingCredits:(id)sender {
    BookingCreditsViewController *bookingCredits = [[BookingCreditsViewController alloc]initWithNibName:@"BookingCreditsViewController" bundle:nil];
    bookingCredits.checkValue = 1;
    [self.navigationController pushViewController:bookingCredits animated:YES];
}
-(void)addDataInFields
{
    txt_firstName.text = self.parentInfo.parentFirstName;
    txt_lastName.text = self.parentInfo.parentLastName;
    txt_email.text = self.parentInfo.parentUserName;
    txt_phone1.text=[self.numFormatter formatText:self.parentInfo.parentPhone];
    txt_phone2.text= [self.numFormatter formatText:self.parentInfo.parentLocalPhone];
    txt_relationship.text = self.parentInfo.parentRelationship;
    
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

                [self.navigationController popViewControllerAnimated:YES];
            }
            else
            {
                
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                
            }
            break;
        case 6:
            [self logout:dict_responseObj];
            break;
            
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription]];
    
}
#pragma mark -- AlertView delegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    
    if (alertView.tag==100 && buttonIndex==1) {
        KidsProfileViewController *kidsProfile = [[KidsProfileViewController alloc]initWithNibName:@"KidsProfileViewController" bundle:nil];
        kidsProfile.checkValue = 1;
        [self.navigationController pushViewController:kidsProfile animated:YES];
    }
    [self logoutAlert:alertView clickedButtonAtIndex:buttonIndex];
}
@end
