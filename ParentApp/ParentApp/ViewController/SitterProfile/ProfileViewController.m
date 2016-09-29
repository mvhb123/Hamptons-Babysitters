//
//  ProfileViewController.m
//  SitterApp
//
//  Created by Vikram gour on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "ProfileViewController.h"
#import "JobDetailViewController.h"
@interface ProfileViewController ()

@end

@implementation ProfileViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.numFormatter = [[NumberFormatter alloc] initWithRegionCode:@"US"];
    self.navigationItem.title = @"Sitter Profile";
    self.array_CertAndTraining=[[NSMutableArray alloc]init];
    self.array_Language=[[NSMutableArray alloc]init];
    self.array_Area=[[NSMutableArray alloc]init];
    self.array_ChildPreferences=[[NSMutableArray alloc]init];
    self.array_OtherPreferences=[[NSMutableArray alloc]init];
    if (!arrayForSelectedSection) {
        arrayForSelectedSection    = [NSMutableArray arrayWithObjects:[NSNumber numberWithBool:NO],
                                      [NSNumber numberWithBool:NO],
                                      [NSNumber numberWithBool:NO],
                                      [NSNumber numberWithBool:NO],
                                      [NSNumber numberWithBool:NO] , nil];
    }
   // self.view.backgroundColor=kBackgroundColor;
   // view_mainBG.backgroundColor=kBackgroundColor;
    self.tblAdditionalInfo.separatorStyle=UITableViewCellSeparatorStyleNone;
    self.view.backgroundColor=kViewBackGroundColor;

   
     // jobDetail.numFormatter
      [self callSitterProfileAPI];
     [self tapEmailAndPhoneGesture];
}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    [self.array_CertAndTraining removeAllObjects];
    [self.array_Language removeAllObjects];
    [self.array_ChildPreferences removeAllObjects];
    [self.array_OtherPreferences removeAllObjects];
    [self.array_Area removeAllObjects];

}

-(void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];

}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)callSitterProfileAPI
{
    self.parentInfo = [ApplicationManager getInstance].parentInfo;
    NSMutableDictionary *dict_sitterProfileData = [[NSMutableDictionary alloc]init];
    [dict_sitterProfileData setSafeObject:self.parentInfo.tokenData forKey:kToken];
    [dict_sitterProfileData setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
    [dict_sitterProfileData setSafeObject:self.str_sitterId forKey:ksitterid];
    [dict_sitterProfileData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [self startNetworkActivity:NO];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kSitterDetailAPI];
    DDLogInfo(@"%@",kSitterDetailAPI);
    networkManager.delegate = self;
    [networkManager sitterDetail:dict_sitterProfileData forRequestNumber:1];
}


-(void)setDataInView
{
        self.sitterInfo=[ApplicationManager getInstance].sitterInfo;
        [imgUserProfile loadImageFromURL:self.sitterInfo.sitterProfileImageUrl];
       // imgUserProfile.layer.cornerRadius = imgUserProfile.frame.size.height/2;
        //imgUserProfile.clipsToBounds = YES;
    
    
        lbl_sitterName.text=self.sitterInfo.sitterName;
        lbl_sitterName.font = [UIFont fontWithName:RobotoRegularFont size:HeadingFieldFontSize];
        lbl_Phone1.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
        lbl_Phone2.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
        lbl_Phone1Value.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
        lbl_Phone2Value.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
        lbl_email.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
        lbl_emailValue.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
        lbl_aboutMe.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
        txtAboutMe.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    
    [lbl_sitterName setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_Phone1 setTextColor:kLabelColor];
    [lbl_Phone2 setTextColor:kLabelColor];
    [lbl_Phone1Value setTextColor:kLabelColor];
    [lbl_Phone2Value setTextColor:kLabelColor];
    [lbl_email setTextColor:kLabelColor];
    [lbl_emailValue setTextColor:kLabelColor];
    [lbl_aboutMe setTextColor:kLabelColor];
    [txtAboutMe setTextColor:kLabelColor];
    
       lbl_Phone1Value.text=[self.numFormatter formatText:[NSString stringWithFormat:@"%@",self.sitterInfo.sitterPhone1]];
        if ([self.sitterInfo.sitterPhone2 isEqualToString:@""]) {
            lbl_email.text = @"";
            lbl_emailValue.text = @"";
            lbl_Phone2.text = @"Email:";
            lbl_Phone2Value.text = [NSString stringWithFormat:@"%@",self.sitterInfo.sitterEmail];
            img_call.image = [UIImage imageNamed:@"Email"];
            img_email.image = nil;
            lbl_line.hidden = true;
            constarinUpper.constant = -25.0;
           // self.tblAdditionalInfo.frame =  CGRectMake(0,200, self.tblAdditionalInfo.frame.size.width,self.tblAdditionalInfo.frame.size.height+100);
        }
        else
        {
            lbl_Phone2Value.text=[self.numFormatter formatText:[NSString stringWithFormat:@"%@",self.sitterInfo.sitterPhone2]];
            lbl_emailValue.text=[NSString stringWithFormat:@"%@",self.sitterInfo.sitterEmail];
        }
    
        lbl_aboutMe.text=[NSString stringWithFormat:@"About Me:"];
        txtAboutMe.text=[NSString stringWithFormat:@"%@",self.sitterInfo.sitterAboutMe];
        for (int i=0; i<=self.sitterInfo.array_Certificates.count; i++) {
        if ([[[self.sitterInfo.array_Certificates safeObjectAtIndex:i]safeObjectForKey:kIsSelected]isEqualToString:@"yes"]) {
            [self.array_CertAndTraining addObject:[self.sitterInfo.array_Certificates safeObjectAtIndex:i]];
          }
        
        }
        self.array_Area=[self.sitterInfo.array_Area mutableCopy];
        self.array_Language=[self.sitterInfo.array_Language mutableCopy];
        self.array_ChildPreferences=[self.sitterInfo.array_Child_preferences mutableCopy];
        self.array_OtherPreferences=[self.sitterInfo.array_Other_preferences mutableCopy];
        [self.tblAdditionalInfo reloadData];
    [txtAboutMe setContentOffset:CGPointMake(0, 0)];
}
-(void)tapEmailAndPhoneGesture
{
    UITapGestureRecognizer *tapped3;
    UITapGestureRecognizer *tapped1 = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(emailTapped:)];
    UITapGestureRecognizer *tapped2 = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(phoneTapped1:)];
    if ([self.sitterInfo.sitterPhone2 isEqualToString:@""]) {
        tapped3 = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(emailTapped:)];
    }
    else
    {
        tapped3 = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(phoneTapped2:)];
    }
    tapped1.numberOfTapsRequired = 1;
    tapped2.numberOfTapsRequired = 1;
    tapped3.numberOfTapsRequired = 1;
    [lbl_emailValue setTag:1];
    [lbl_Phone1Value setTag:2];
    [lbl_Phone2Value setTag:3];
    [lbl_emailValue addGestureRecognizer:tapped1];
    [lbl_Phone1Value addGestureRecognizer:tapped2];
    [lbl_Phone2Value addGestureRecognizer:tapped3];
    lbl_Phone1Value.userInteractionEnabled = YES;
    lbl_emailValue.userInteractionEnabled = YES;
    lbl_Phone2Value.userInteractionEnabled = YES;
    
}
-(void)emailTapped:(UIGestureRecognizer*)gesture
{
    DDLogInfo(@">>> %ld", (long)gesture.view.tag);
    if ([MFMailComposeViewController canSendMail]) {
        MFMailComposeViewController * emailController = [[MFMailComposeViewController alloc] init];
        [emailController.navigationBar setTintColor:[UIColor blackColor]];
        emailController.mailComposeDelegate = self;
        if ([self.sitterInfo.sitterPhone2 isEqualToString:@""]) {
          [emailController setToRecipients:[NSArray arrayWithObjects:lbl_Phone2Value.text,nil]];
        }
        else
        [emailController setToRecipients:[NSArray arrayWithObjects:lbl_emailValue.text,nil]];
        [[emailController navigationBar]setTitleTextAttributes:[NSDictionary dictionaryWithObjectsAndKeys:[UIColor grayColor], NSForegroundColorAttributeName, nil]];
        NSMutableString *emailBody = [[NSMutableString alloc] initWithString:@"<html><body>"];
        //Add some text to it however you want
        [emailBody appendString:@"<p></p>"];
        [emailBody appendString:@"<br>"];
        
        [emailBody appendString:@"</body></html>"];
        [emailBody appendString:@"<br>"];
        [emailController setMessageBody:emailBody isHTML:YES];
        
        [self presentViewController:emailController animated:YES completion:nil];
    }
    else {
        UIAlertView * alertView = [[UIAlertView alloc] initWithTitle:@"Warning" message:kMustMailAccount delegate:nil cancelButtonTitle:NSLocalizedString(@"OK", @"OK") otherButtonTitles:nil];
        [alertView show];
        
    }
    
}
-(void)phoneTapped1:(UIGestureRecognizer*)gesture
{
    DDLogInfo(@">>> %ld", (long)gesture.view.tag);
    NSURL *phoneUrl = [NSURL URLWithString:[NSString stringWithFormat:@"telprompt:%@",self.sitterInfo.sitterPhone1]];
    
    if ([[UIApplication sharedApplication] canOpenURL:phoneUrl]) {
        [[UIApplication sharedApplication] openURL:phoneUrl];
    } else {
        UIAlertView * calert = [[UIAlertView alloc]initWithTitle:@"Alert" message:kCallFacilityNotAvailable delegate:nil cancelButtonTitle:@"ok" otherButtonTitles:nil, nil];
        [calert show];
    }
    
    
}
-(void)phoneTapped2:(UIGestureRecognizer*)gesture
{
    if ([self.sitterInfo.sitterPhone2 isEqualToString:@""]) {
        [self emailTapped:gesture];
    }
    else
    {
    DDLogInfo(@">>> %ld", (long)gesture.view.tag);
    NSURL *phoneUrl = [NSURL URLWithString:[NSString stringWithFormat:@"telprompt:%@",self.sitterInfo.sitterPhone2]];
    
    if ([[UIApplication sharedApplication] canOpenURL:phoneUrl]) {
        [[UIApplication sharedApplication] openURL:phoneUrl];
    } else {
        UIAlertView * calert = [[UIAlertView alloc]initWithTitle:@"Alert" message:kCallFacilityNotAvailable delegate:nil cancelButtonTitle:@"ok" otherButtonTitles:nil, nil];
        [calert show];
    }
  }
}
#pragma mark - MFMailComposeViewControllerDelegate

- (void)mailComposeController:(MFMailComposeViewController *)controller didFinishWithResult:(MFMailComposeResult)result error:(NSError *)error {
    NSInteger results = result;
    switch(results){
        case MFMailComposeResultSent:{
            [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEmailSendSuccessFully];
            break;
        }
        case MFMailComposeResultCancelled:{
            [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEmailSendingCancled];
            break;
        }
        case MFMailComposeResultFailed:{
            [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:kEmailSendingFail];
            break;
        }
    }
    [controller dismissViewControllerAnimated:YES completion:^{
        
    }];
}
#pragma mark UITableViewDatasource

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView{
    return 5;
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 40;
}

- (NSInteger)tableView:(UITableView *)table numberOfRowsInSection:(NSInteger)section
{
     if ([[arrayForSelectedSection objectAtIndex:section] boolValue]) {
    if (section==0) {
        return [self.array_CertAndTraining count];
    }
    if (section==1) {
        return [self.array_Area count];
    }
    if (section==2) {
        return [self.array_Language count];
    }
    if (section==3) {
        return [self.array_OtherPreferences count];
    }
    if (section==4) {
        return [self.array_ChildPreferences count];
    }
  }
    return 0;
     
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *cellIdentifier = @"certAndTrainingCell";
    static NSString *otherCellIdentifier = @"otherCell";

    CertificatesAndTrainingCell *cell = [tableView dequeueReusableCellWithIdentifier:cellIdentifier];
    UITableViewCell *otherCell=[tableView dequeueReusableCellWithIdentifier:otherCellIdentifier];
    if (indexPath.section==0) {
        if (cell == nil)
        {
            NSArray *nibArray=[[NSBundle mainBundle] loadNibNamed:@"CertificatesAndTrainingCell" owner:self options:nil];
            cell = [nibArray safeObjectAtIndex:0];
            [cell setBackgroundColor:[UIColor clearColor]];
            cell.accessoryType=UITableViewCellAccessoryNone;
            [cell.lbl_certType setTextColor:kLabelColor];
            [cell.lbl_certValue setTextColor:kLabelColor];
            [cell.lbl_certType setFont:[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize]];
            [cell.lbl_certValue setFont:[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize]];
            
            [cell.lbl_certType setText:@""];
            [cell.lbl_certValue setText:@""];

            
        }
         cell.selectionStyle = UITableViewCellSelectionStyleNone;
        NSDictionary *d=[self.array_CertAndTraining objectAtIndex:indexPath.row];
        [cell.lbl_certType setText:[d valueForKey:kName]];
        if ([[d valueForKey:kDate] isEqualToString:@"NA"]) {
            [cell.lbl_certValue setText:[NSString stringWithFormat:@"%@",[d valueForKey:kIsSelected]]];
        }else{
            [cell.lbl_certValue setText:[NSString stringWithFormat:@"%@,%@",[d valueForKey:kIsSelected],[d valueForKey:kDate]]];
        }
       return cell;
        
    }
    if (indexPath.section==1) {
        if (otherCell == nil) {
            otherCell=[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:otherCellIdentifier];
             otherCell.textLabel.textColor = kLabelColor;
        }
        otherCell.selectionStyle = UITableViewCellSelectionStyleNone;
        otherCell.textLabel.font=[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
        otherCell.backgroundColor = kBackgroundColor;
        NSDictionary *dictTemp=[self.array_Area safeObjectAtIndex:indexPath.row];
       otherCell.textLabel.text=[dictTemp objectForKey:kPreferName];
        return otherCell;
    }if (indexPath.section==2) {
        if (otherCell == nil) {
            otherCell=[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:otherCellIdentifier];
            otherCell.textLabel.textColor = kLabelColor;
        }
        otherCell.selectionStyle = UITableViewCellSelectionStyleNone;
        otherCell.textLabel.font=[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
        otherCell.backgroundColor = kBackgroundColor;
        NSDictionary *dictTemp=[self.array_Language safeObjectAtIndex:indexPath.row];
        otherCell.textLabel.text=[dictTemp objectForKey:kPreferName];
        return otherCell;
    }if (indexPath.section==3) {
        if (otherCell == nil) {
            otherCell=[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:otherCellIdentifier];
             otherCell.textLabel.textColor = kLabelColor;
        }
        otherCell.selectionStyle = UITableViewCellSelectionStyleNone;
        otherCell.textLabel.font=[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
        otherCell.backgroundColor = kBackgroundColor;
        NSDictionary *dictTemp=[self.array_OtherPreferences safeObjectAtIndex:indexPath.row];
        otherCell.textLabel.text=[dictTemp objectForKey:kPreferName];
        return otherCell;
    }
    if (indexPath.section==4) {
        if (otherCell == nil) {
            otherCell=[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:otherCellIdentifier];
             otherCell.textLabel.textColor = kLabelColor;
        }
        otherCell.selectionStyle = UITableViewCellSelectionStyleNone;
        otherCell.textLabel.font=[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
        otherCell.backgroundColor = kBackgroundColor;
        NSDictionary *dictTemp=[self.array_ChildPreferences safeObjectAtIndex:indexPath.row];
        otherCell.textLabel.text=[dictTemp objectForKey:kPreferName];
        return otherCell;
    }
    return nil;
}

- (UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section{
    UIView *viewHeader=[[UIView alloc]initWithFrame:CGRectMake(0, 0,self.tblAdditionalInfo.frame.size.width , 40)];
    [viewHeader setBackgroundColor:[UIColor clearColor]];
    viewHeader.tag                  = section;
    UIImageView *imgHeaderBg=[[UIImageView alloc]initWithImage:[UIImage imageNamed:@"accordian.png"]];
    [imgHeaderBg setFrame:viewHeader.frame];
    [viewHeader addSubview:imgHeaderBg];
    CGRect lblFrm=viewHeader.frame;
    lblFrm.origin.x=20;
    UILabel *lblHeaderTitle=[[UILabel alloc]initWithFrame:lblFrm];
    NSString *strTitle=@"";
    if (section==0) {
        strTitle= @"Skills & Certifications";
    }else if (section==1) {
        strTitle =@"Areas";
    }else if (section==2) {
        strTitle= @"Languages";
    }else if (section==3) {
        strTitle= @"Other Preferences";
    }else{
        strTitle= @"Child Preferences";
    }
    lblHeaderTitle.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    [lblHeaderTitle setTextColor:kColorWhite];
    [lblHeaderTitle setText:strTitle];
    [viewHeader addSubview:lblHeaderTitle];
    
    UITapGestureRecognizer  *headerTapped   = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(sectionHeaderTapped:)];
    [viewHeader addGestureRecognizer:headerTapped];
    
    
    BOOL manyCells                  = [[arrayForSelectedSection objectAtIndex:section] boolValue];
    //up or down arrow depending on the bool
    UIImageView *upDownArrow        = [[UIImageView alloc] initWithImage:manyCells ? [UIImage imageNamed:@"arrowDown"]:[UIImage imageNamed:@"arrowUp"]];
    upDownArrow.autoresizingMask    = UIViewAutoresizingFlexibleLeftMargin;
    upDownArrow.frame               = CGRectMake(viewHeader.frame.size.width-30,12.5, 15, 15);
    [viewHeader addSubview:upDownArrow];
    return viewHeader;
}

- (UIView *)tableView:(UITableView *)tableView viewForFooterInSection:(NSInteger)section{
    UIView *footer  = [[UIView alloc] initWithFrame:CGRectZero];
    return footer;
}

- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section{
    return 40;
}
- (CGFloat)tableView:(UITableView *)tableView heightForFooterInSection:(NSInteger)section{
    return 0;
}

#pragma mark - gesture tapped
- (void)sectionHeaderTapped:(UITapGestureRecognizer *)gestureRecognizer{
    NSIndexPath *indexPath = [NSIndexPath indexPathForRow:0 inSection:gestureRecognizer.view.tag];
    if (indexPath.row == 0) {
        BOOL collapsed  = [[arrayForSelectedSection objectAtIndex:indexPath.section] boolValue];
        collapsed       = !collapsed;
        [arrayForSelectedSection replaceObjectAtIndex:indexPath.section withObject:[NSNumber numberWithBool:collapsed]];
        
        //reload specific section animated
        NSRange range   = NSMakeRange(indexPath.section, 1);
        NSIndexSet *sectionToReload = [NSIndexSet indexSetWithIndexesInRange:range];
        [self.tblAdditionalInfo reloadSections:sectionToReload withRowAnimation:UITableViewRowAnimationNone];
    }
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
                // [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kMessage]];
                self.parentInfo.tokenData = [[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kTokenData];
                [[ApplicationManager getInstance]saveSitterDetail:dict_responseObj];
                [self setDataInView];
                
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
    DDLogInfo(@"%@",error);
    //[[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription]];
    [self showAlertForSelf:self withTitle:nil andMessage:[error localizedDescription]];
    
}
@end

