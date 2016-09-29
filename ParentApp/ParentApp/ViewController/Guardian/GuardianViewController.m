//
//  GuardianViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 15/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "GuardianViewController.h"
#import "RegistrationViewController.h"
#import "EditProfileViewController.h"

@interface GuardianViewController ()

@end

@implementation GuardianViewController
@synthesize dict_updateProfileData;
- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.numFormatter = [[NumberFormatter alloc] initWithRegionCode:@"US"];
    [[self navigationController] setNavigationBarHidden:NO animated:YES];
    self.navigationItem.title = @"Other Contacts";
    self.view.backgroundColor=kViewBackGroundColor;
    NavigationBarRightButton;
    [self setOtherContacts];
    [self setFontTypeAndFontSize];
    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    contentHight = 0;
    UIView *lLast = [view_otherGurdian.subviews lastObject];
    NSInteger wd = lLast.frame.origin.y;
    NSInteger ht = lLast.frame.size.height;
    contentHight = wd+ht;
    self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+20);
    
}
#pragma mark Textfield delegate methods
// when editing is started in textfield.
- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string {
    if (textField == txt_phone1 || textField == txt_phone2 || textField == txt_emergencyContactPhone1) {
        textField.text = [self.numFormatter formatText:textField.text];
        return YES;
    }
    
    return YES;
}
- (void)textFieldDidBeginEditing:(UITextField *)textField{
    
    
}
- (BOOL)textFieldShouldReturn:(UITextField *)textField{
    if (textField!=txt_emergencyContactPhone1){
        [[self.view viewWithTag:textField.tag+1] becomeFirstResponder];
    }
    return [textField resignFirstResponder];
}
- (void)onTouchOnBackground:(UITapGestureRecognizer*)sender {
    [self.view endEditing:YES];
    [self.backgroundScrollView setContentOffset:CGPointMake(0, 0)];
}
#pragma mark-UserDefineMethods
-(void)setFontTypeAndFontSize
{
//    lbl_gurdName.font=[UIFont fontWithName:RobotoCondensedFont size:LabelFieldFontSize];
//    lbl_gurdRelationship.font=[UIFont fontWithName:RobotoCondensedFont size:LabelFieldFontSize];
//    lbl_gurdPhone1.font=[UIFont fontWithName:RobotoCondensedFont size:LabelFieldFontSize];
//    lbl_gurdPhone2.font = [UIFont fontWithName:RobotoCondensedFont size:LabelFieldFontSize];
//    lbl_emerName.font = [UIFont fontWithName:RobotoCondensedFont size:LabelFieldFontSize];
//    lbl_emerPhone1.font = [UIFont fontWithName:RobotoCondensedFont size:LabelFieldFontSize];
//    lbl_emerRelationship.font = [UIFont fontWithName:RobotoCondensedFont size:LabelFieldFontSize];
//    lbl_gurdianHeader.font = [UIFont fontWithName:RobotoCondensedFont size:ButtonFieldFontSize];
//    lbl_emergencyContactHeader.font = [UIFont fontWithName:RobotoCondensedFont size:ButtonFieldFontSize];
    
    lbl_gurdName.textColor=kLabelColor;
    lbl_gurdRelationship.textColor=kLabelColor;
    lbl_gurdPhone1.textColor=kLabelColor;
    lbl_gurdPhone2.textColor=kLabelColor;
    lbl_emerName.textColor=kLabelColor;
    lbl_emerPhone1.textColor=kLabelColor;
    lbl_emerRelationship.textColor=kLabelColor;
    lbl_emergencyContactHeader.textColor=UIColorFromHexCode(0x0b0077);
    lbl_gurdianHeader.textColor=UIColorFromHexCode(0x0b0077);

    
    
}
-(void)saveAction:(UIBarButtonItem *)sender{
    [self.view endEditing:YES];
    NSString *str_phone1 = [self.numFormatter rawText:txt_phone1.text];
    NSString *str_phone2 = [self.numFormatter rawText:txt_phone2.text];
    NSString *str_emergencyPhone = [self.numFormatter rawText:txt_emergencyContactPhone1.text];
    if (checkData == 1) {
        if ([self checkUserData])
        {
        NSArray *array = [self.navigationController viewControllers];
        EditProfileViewController *editView =  self.navigationController.viewControllers[[array count]-2];
        [editView.dict_updateProfileData setSafeObject:txt_firstName.text forKey:kSpouseFirstName];
        [editView.dict_updateProfileData setSafeObject:txt_relationship.text forKey:kSpouseRelation];
        [editView.dict_updateProfileData setSafeObject:str_phone1 forKey:kSpousePhone1];
        [editView.dict_updateProfileData setSafeObject:str_phone2 forKey:kSpousePhone2];
        [editView.dict_updateProfileData setSafeObject:txt_emergencyContactNAme.text forKey:kEmergencycontactName];
        [editView.dict_updateProfileData setSafeObject:txt_emergencyContactRelationship.text forKey:kEmergencyRelation];
        [editView.dict_updateProfileData setSafeObject:str_emergencyPhone forKey:kEmergencyPhone];
        editView.chaeckData = 2;
        [self.navigationController popViewControllerAnimated:YES];
        }
        
    }
    else
    {
        if ([self checkUserData]) {
            
            RegistrationViewController *registrationView =  self.navigationController.viewControllers[1];
            [registrationView.dict_loginData setSafeObject:txt_firstName.text forKey:kSpouseFirstName];
            [registrationView.dict_loginData setSafeObject:txt_relationship.text forKey:kSpouseRelation];
            [registrationView.dict_loginData setSafeObject:str_phone1 forKey:kSpousePhone1];
            [registrationView.dict_loginData setSafeObject:str_phone2 forKey:kSpousePhone2];
            [registrationView.dict_loginData setSafeObject:txt_emergencyContactNAme.text forKey:kEmergencycontactName];
            [registrationView.dict_loginData setSafeObject:txt_emergencyContactRelationship.text forKey:kEmergencyRelation];
            [registrationView.dict_loginData setSafeObject:str_emergencyPhone forKey:kEmergencyPhone];
            [self.navigationController popViewControllerAnimated:YES];
            
        }
    }
}
-(BOOL)checkUserData
{
    BOOL isvalid = NO;
   // isvalid = NO;
    txt_emergencyContactNAme.text = trimedString(txt_emergencyContactNAme.text);
    txt_emergencyContactPhone1.text = trimedString(txt_emergencyContactPhone1.text);
    if ([txt_firstName.text isEqualToString:@""] && (!([txt_phone1.text isEqualToString:@""])||!([txt_phone2.text isEqualToString:@""])||!([txt_relationship.text isEqualToString:@""]))) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterGuardianName];
        isvalid=NO;
    }
   else if (![txt_firstName.text isEqualToString:@""]&&([txt_relationship.text isEqualToString:@""]))
   {
       [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterRelationshipGuardianFields];
       isvalid=NO;
 
   }else if (![txt_firstName.text isEqualToString:@""]&&([txt_phone1.text isEqualToString:@""]))
   {
       [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterPhoneGuardianFields];
       isvalid=NO;
       
   }
   else if (txt_emergencyContactNAme==nil|| [txt_emergencyContactNAme.text isEqualToString:@""]) {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterEmergencyContact];
        isvalid=NO;
    }
    else if (txt_emergencyContactPhone1.text==nil|| [txt_emergencyContactPhone1.text isEqualToString:@""])
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterEmergencyContactNumber];
        isvalid = NO;
    }
    else if ([[self.numFormatter rawText:txt_emergencyContactPhone1.text] length]<6)
    {
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kEnterValidMobileNumber];
        isvalid = NO;
    }
    else
    {
        isvalid = YES;
    }
    return isvalid;
}
-(void)setOtherContacts
{
    if (self.CheckFirstTimeEdit == 1) {
        checkData = 1;
        txt_firstName.text = self.parentInfo.parentGurdianName;
        txt_relationship.text = self.parentInfo.parentGurdianRelationship;
        txt_phone1.text=[self.numFormatter formatText:self.parentInfo.parentGurdianPhone1];
        txt_phone2.text= [self.numFormatter formatText:self.parentInfo.parentGurdianPhone2];
        txt_emergencyContactNAme.text = self.parentInfo.parentEmergencyContactName;
        txt_emergencyContactPhone1.text = [self.numFormatter formatText:self.parentInfo.parentEmergencyPhone];
        txt_emergencyContactRelationship.text = self.parentInfo.parentEmergencyRelation;
    }
    
    if (self.dict_savedUpdatedData.count>0) {
        txt_firstName.text = [self.dict_savedUpdatedData safeObjectForKey:kSpouseFirstName];
        txt_relationship.text = [self.dict_savedUpdatedData safeObjectForKey:kSpouseRelation];
        txt_phone1.text=[self.numFormatter formatText:[self.dict_savedUpdatedData safeObjectForKey:kSpousePhone1]];
        txt_phone2.text=[self.numFormatter formatText:[self.dict_savedUpdatedData safeObjectForKey:kSpousePhone2]];
        txt_emergencyContactNAme.text = [self.dict_savedUpdatedData safeObjectForKey:kEmergencycontactName];
        txt_emergencyContactPhone1.text =[self.numFormatter formatText:[self.dict_savedUpdatedData safeObjectForKey:kEmergencyPhone]];
        txt_emergencyContactRelationship.text = [self.dict_savedUpdatedData safeObjectForKey:kEmergencyRelation];
        checkData = 1;
    }
    
    if (self.dict_loginData.count>0) {
        txt_emergencyContactNAme.text = [self.dict_loginData safeObjectForKey:kEmergencycontactName];
        txt_emergencyContactPhone1.text =[self.numFormatter formatText:[self.dict_loginData safeObjectForKey:kEmergencyPhone]];
        txt_emergencyContactRelationship.text = [self.dict_loginData safeObjectForKey:kEmergencyRelation];
        txt_firstName.text = [self.dict_loginData safeObjectForKey:kSpouseFirstName];
        txt_phone1.text = [self.numFormatter formatText:[self.dict_loginData safeObjectForKey:kSpousePhone1]];
        txt_phone2.text = [self.numFormatter formatText:[self.dict_loginData safeObjectForKey:kSpousePhone2]];
        txt_relationship.text = [self.dict_loginData safeObjectForKey:kSpouseRelation];
    }
    
}

@end
