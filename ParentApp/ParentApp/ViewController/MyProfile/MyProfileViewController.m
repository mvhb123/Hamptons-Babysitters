//
//  MyProfileViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 08/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "MyProfileViewController.h"
#import "RegistrationViewController.h"
#import "ViewKidsProfileViewController.h"
#import "EditProfileViewController.h"
#import "KidsProfileViewController.h"
#import "HomePageViewController.h"
#import "BookingCreditsViewController.h"
#import "PaymentViewController.h"


@interface MyProfileViewController ()

@end

@implementation MyProfileViewController
@synthesize dict_parentRecord;

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.navigationItem.title = @"My Profile";
    self.numFormatter = [[NumberFormatter alloc] initWithRegionCode:@"US"];
    self.view.backgroundColor=kViewBackGroundColor;
    view_profile.backgroundColor = kViewBackGroundColor;
    view_bottom.backgroundColor = kViewBackGroundColor;
    view_emergencyContact.backgroundColor = kViewBackGroundColor;
    self.backgroundScrollView.backgroundColor = kViewBackGroundColor;
    UILabel *titleLabel = [[UILabel alloc] init];
    titleLabel.text = @"My Profile";
    [titleLabel setTextColor:[UIColor whiteColor]];
    [titleLabel sizeToFit];
    self.navigationItem.titleView = titleLabel;
}
-(void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    self.parentInfo = [ApplicationManager getInstance].parentInfo;
    [self setProfile];
    [self setFontTypeAndFontSize];
   }
-(void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    [view_profile addSubview:view_emergencyContact];
    [self.backgroundScrollView addSubview:view_bottom];

   
}
-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    contentHight = 0;
      if ([self.parentInfo.parentGurdianName isEqualToString:@""]) {
        lbl_viewOtherGurdianNAme.hidden = true;
        lbl_viewRelationship.hidden = true;
        lbl_viewgurdianphone1.hidden = true;
        lbl_viewGurdianPhone2.hidden = true;
          lbl_otherContacts.hidden=true;
        view_emergencyContact.frame = CGRectMake(view_emergencyContact.frame.origin.x,lbl_otherContacts.frame.origin.y,view_profile.frame.size.width,110);
        view_bottom.frame = CGRectMake(view_bottom.frame.origin.x,view_emergencyContact.frame.origin.y+view_emergencyContact.frame.size.height,view_profile.frame.size.width, 220);
          if ([self.parentInfo.parentEmergencyRelation isEqualToString:@""]) {
              lbl_viewEmergencyContactRelationship.text = @"Phone 1";
              lbl_viewEmergencyPhone1.text = @"";
              lbl_emergencyContactRelationship.text = [self.numFormatter formatText:self.parentInfo.parentEmergencyPhone];
              lbl_emergencyContactPhone1.text = @"";
            view_bottom.frame = CGRectMake(view_bottom.frame.origin.x,view_emergencyContact.frame.origin.y+view_emergencyContact.frame.size.height-30,view_profile.frame.size.width, 268);
          }
          else
          {
              
            lbl_viewEmergencyPhone1.text = @"Phone 1";
            lbl_viewEmergencyContactRelationship.text = @"Relationship";
            view_bottom.frame = CGRectMake(view_bottom.frame.origin.x,view_emergencyContact.frame.origin.y+view_emergencyContact.frame.size.height,view_profile.frame.size.width, 268);
          }
          [view_profile setFrame:CGRectMake(view_profile.frame.origin.x, view_profile.frame.origin.y, view_profile.frame.size.width, 1000)];
          self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,600);
          // self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,480);
          
    }
    else
    {
        lbl_viewOtherGurdianNAme.hidden = false;
        lbl_viewRelationship.hidden = false;
        lbl_viewgurdianphone1.hidden = false;
        lbl_viewGurdianPhone2.hidden = false;
        lbl_otherContacts.hidden=false;
        if ([self.parentInfo.parentGurdianPhone2 isEqualToString:@""]) {
              view_emergencyContact.frame = CGRectMake(view_emergencyContact.frame.origin.x,lbl_viewGurdianPhone2.frame.origin.y,view_profile.frame.size.width,137);
            lbl_viewGurdianPhone2.hidden = true;
        }
        else
             view_emergencyContact.frame = CGRectMake(view_emergencyContact.frame.origin.x,lbl_viewGurdianPhone2.frame.origin.y+lbl_viewGurdianPhone2.frame.size.height,view_profile.frame.size.width,137);
            
        if ([self.parentInfo.parentEmergencyRelation isEqualToString:@""]) {
            lbl_viewEmergencyContactRelationship.text = @"Phone 1";
            lbl_viewEmergencyPhone1.text = @"";
            lbl_emergencyContactRelationship.text = [self.numFormatter formatText:self.parentInfo.parentEmergencyPhone];
            lbl_emergencyContactPhone1.text = @"";
            view_bottom.frame = CGRectMake(view_bottom.frame.origin.x,view_emergencyContact.frame.origin.y+view_emergencyContact.frame.size.height-30,view_profile.frame.size.width, 258);
        }
        else
        {
            
            lbl_viewEmergencyPhone1.text = @"Phone 1";
            lbl_viewEmergencyContactRelationship.text = @"Relationship";
            view_bottom.frame = CGRectMake(view_bottom.frame.origin.x,view_emergencyContact.frame.origin.y+view_emergencyContact.frame.size.height-30,view_profile.frame.size.width,258);

        }
   [view_profile setFrame:CGRectMake(view_profile.frame.origin.x, view_profile.frame.origin.y, view_profile.frame.size.width, 1000)];
    self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,view_bottom.frame.origin.y+view_bottom.frame.size.height);
    }
//    UIView *lLast = [view_profile.subviews lastObject];
//    NSInteger wd = lLast.frame.origin.y;
//    NSInteger ht = lLast.frame.size.height;
//    contentHight = wd+ht;
//     self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+310);
   self.backgroundScrollView.scrollEnabled = YES;
}
- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)setFontTypeAndFontSize
{
    lbl_phone1.font=[UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_phone2.font=[UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_email.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    
    lbl_viewemail.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_viewPhone2.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_viewPhone1.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    
    lbl_userName.font=[UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    lbl_otherContacts.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    lbl_viewJobAddress.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    lbl_JobAddress.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_viewOtherGurdianNAme.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_otherGurdianName.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_viewgurdianphone1.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_gurdianPhone1.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_viewGurdianPhone2.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_gurdianPhone2.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_viewRelationship.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_relationship.font=[UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_viewEmargencyContactName.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_emergencyContactName.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_viewEmergencyContactRelationship.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_emergencyContactRelationship.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_viewEmergencyPhone1.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_guardian.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    lbl_emergencyContact.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    
    lbl_emergencyContactPhone1.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    //btn_bookingCredits.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
   // btn_childrenProfile.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
   // btn_creditsCardDetails.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    
    [lbl_phone1 setTextColor:kLabelColor];
    [lbl_phone2 setTextColor:kLabelColor];
    [lbl_email setTextColor:kLabelColor];
    [lbl_viewPhone2 setTextColor:kLabelColor];
    [lbl_viewPhone1 setTextColor:kLabelColor];
    [lbl_viewemail setTextColor:kLabelColor];
    
    [lbl_otherGurdianName setTextColor:kLabelColor];
    [lbl_viewOtherGurdianNAme setTextColor:kLabelColor];
    [lbl_viewRelationship setTextColor:kLabelColor];
    [lbl_relationship setTextColor:kLabelColor];
    [lbl_gurdianPhone1 setTextColor:kLabelColor];
    [lbl_gurdianPhone2 setTextColor:kLabelColor];
    [lbl_viewgurdianphone1 setTextColor:kLabelColor];
    [lbl_viewGurdianPhone2 setTextColor:kLabelColor];
    
    [lbl_emergencyContactName setTextColor:kLabelColor];
    [lbl_viewEmargencyContactName setTextColor:kLabelColor];
    [lbl_emergencyContactRelationship setTextColor:kLabelColor];
    [lbl_viewEmergencyContactRelationship setTextColor:kLabelColor];
    [lbl_viewEmergencyPhone1 setTextColor:kLabelColor];
    [lbl_emergencyContactPhone1 setTextColor:kLabelColor];
    
    [lbl_JobAddress setTextColor:kLabelColor];
    
    
    [lbl_userName setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_otherContacts setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_guardian setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_viewJobAddress setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_emergencyContact setTextColor:UIColorFromHexCode(0x04005c)];
    [imgUserDetailBG setBackgroundColor:UIColorFromHexCode(0xe8e8e8)];

    
    
    
}
- (IBAction)onClickEditProfile:(id)sender {
    EditProfileViewController *editProfile = [[EditProfileViewController alloc]initWithNibName:@"EditProfileViewController" bundle:nil];
    editProfile.dict_userProfile = [dict_parentRecord mutableCopy];
    [self.navigationController pushViewController:editProfile animated:YES];
}

- (IBAction)onClickKidsProfile:(id)sender {
   
    if ([self.parentInfo.parentChildCountName integerValue] == 0) {
        UIAlertView *emailAlert=[[UIAlertView alloc] initWithTitle:@"" message:kchildNotAdded delegate:self cancelButtonTitle:@"No" otherButtonTitles:@"Yes", nil];
        emailAlert.tag=100;
        [emailAlert show];
        
    }
    else
    {
        ViewKidsProfileViewController *viewkidsProfile = [[ViewKidsProfileViewController alloc]initWithNibName:@"ViewKidsProfileViewController" bundle:nil];
        [self.navigationController pushViewController:viewkidsProfile animated:YES];
        
        
        
    }
   
}
- (IBAction)onClickBookingCredits:(id)sender {
    BookingCreditsViewController *bookingCredits = [[BookingCreditsViewController alloc]initWithNibName:@"BookingCreditsViewController" bundle:nil];
    bookingCredits.checkValue = 1;
    [self.navigationController pushViewController:bookingCredits animated:YES];
}

- (IBAction)onClickCreditCardDetail:(id)sender {
    PaymentViewController *paymentView = [[PaymentViewController alloc]initWithNibName:@"PaymentViewController" bundle:nil];
    paymentView.CheckValue = 1;
    [self.navigationController pushViewController:paymentView animated:YES];
}
-(void)setProfile
{
    [lbl_JobAddress setVerticalAlignment:VerticalAlignmentTop];
    lbl_userName.text = self.parentInfo.parentName;
    lbl_phone1.text = [self.numFormatter formatText:self.parentInfo.parentPhone];
    if ([self.parentInfo.parentLocalPhone isEqualToString:@""]) {
        lbl_viewPhone2.text = @"Email:";
        lbl_phone2.text = self.parentInfo.parentUserName;
        lbl_email.hidden = true;
        lbl_viewemail.hidden = true;
        lbl_line.hidden = true;
        img_mail.hidden = true;
        img_call.image = [UIImage imageNamed:@"Email"];
        
    }
    else
    {
        lbl_line.hidden = false;
        img_mail.hidden = false;
        lbl_email.hidden = false;
        lbl_viewemail.hidden = false;
        lbl_viewPhone2.text = @"Phone 2:";
        img_call.image = [UIImage imageNamed:@"call"];
        lbl_phone2.text = [self.numFormatter formatText:self.parentInfo.parentLocalPhone];
        lbl_email.text = self.parentInfo.parentUserName;
    }
    lbl_otherGurdianName.text = self.parentInfo.parentGurdianName;
    lbl_relationship.text = self.parentInfo.parentGurdianRelationship;
    
    lbl_gurdianPhone1.text = [self.numFormatter formatText:self.parentInfo.parentGurdianPhone1];
    lbl_gurdianPhone2.text = [self.numFormatter formatText:self.parentInfo.parentGurdianPhone2];
    lbl_emergencyContactName.text = self.parentInfo.parentEmergencyContactName;
    lbl_emergencyContactPhone1.text = [self.numFormatter formatText:self.parentInfo.parentEmergencyPhone];
    lbl_emergencyContactRelationship.text = self.parentInfo.parentEmergencyRelation;
    
    NSString *str_localAddress = [NSString stringWithFormat:@""];
    
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",self.parentInfo.StreetAddress]];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"\n%@, ",self.parentInfo.City]];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"\n%@, ",self.parentInfo.State]];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@ ",self.parentInfo.zipCode]];
    if (![self.parentInfo.CrossStreet isEqualToString:@""]) {
        str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",self.parentInfo.CrossStreet]];
    }
    if (![self.parentInfo.HotelName isEqualToString:@""]) {
        str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@",self.parentInfo.HotelName]];
    }
    lbl_JobAddress.text = trimedString(str_localAddress);
    lbl_BillingAddress.text = @"";
    lbl_viewBillingAddress.text = @"";
    
}
#pragma mark - SMCoreNetworkManagerDelegate

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    DDLogInfo(@"%@",responseObject);
    switch (requestId) {
        case 6:
            [self logout:dict_responseObj];
            break;
            
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}
#pragma mark -- UIAlertViewDelegate
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
