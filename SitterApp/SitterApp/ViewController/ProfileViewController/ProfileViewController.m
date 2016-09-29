//
//  ProfileViewController.m
//  SitterApp
//
//  Created by Vikram gour on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AdditionalInfoViewController.h"
@interface ProfileViewController (){
    NSMutableArray      *arrayForSelectedSection;
}
@end

@implementation ProfileViewController
@synthesize sitterInfo;
- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.sitterInfo=[ApplicationManager getInstance].sitterInfo;
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
    self.view.backgroundColor=kBackgroundColor;
    view_mainBG.backgroundColor=kBackgroundColor;
    self.tblAdditionalInfo.separatorStyle=UITableViewCellSeparatorStyleNone;
    lbl_sitterName.text=@"";
    lbl_aboutMe.text=@"";
    [[NSNotificationCenter defaultCenter]addObserver:self selector:@selector(setSitterStatus) name:kNotificationSitterStatus object:nil];

}

-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    self.sitterInfo=[ApplicationManager getInstance].sitterInfo;
    self.navigationItem.title=@"My Profile";
    [self.array_CertAndTraining removeAllObjects];
    [self.array_Language removeAllObjects];
    [self.array_ChildPreferences removeAllObjects];
    [self.array_OtherPreferences removeAllObjects];
    [self.array_Area removeAllObjects];
    for (NSDictionary *d in self.sitterInfo.array_Certificates) {
        if ([[d objectForKey:kIsSelected] isEqualToString:@"yes"]) {
            [self.array_CertAndTraining addObject:[d mutableCopy]];
        }
    }
    self.array_Area=[self.sitterInfo.array_Area mutableCopy];
    self.array_Language=[self.sitterInfo.array_Language mutableCopy];
    self.array_ChildPreferences=[self.sitterInfo.array_Child_preferences mutableCopy];
    self.array_OtherPreferences=[self.sitterInfo.array_Other_preferences mutableCopy];
    [self.tblAdditionalInfo reloadData];
}

-(void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
    //Set font color
    [lbl_emailValue setTextColor:kColorGrayDark];
    [lbl_Phone1Value setTextColor:kColorGrayDark];
    [lbl_Phone2Value setTextColor:kColorGrayDark];
    [lbl_aboutMe setTextColor:kColorGrayLight];
    [lblSitterStatus setTextColor:kColorGrayLight];
    DDLogInfo(@"txt %@",NSStringFromUIEdgeInsets(txtAboutMe.contentInset));
    
    [txtAboutMe setContentInset:UIEdgeInsetsMake(-4,-5,0,0)];
    DDLogInfo(@"img url %@",self.sitterInfo.sitterProfileImageUrl);
    [imgUserProfile loadImageFromURL:self.sitterInfo.sitterProfileImageUrl];
    [lbl_sitterName setFont:[UIFont fontWithName:Font_Roboto_bold size:20.0]];
    lbl_sitterName.textColor = UIColorFromHexCode(0x04004c);
    lbl_sitterName.text=self.sitterInfo.sitterName;
    lbl_Phone1Value.text=[NSString stringWithFormat:@"%@",self.sitterInfo.sitterPhone1];
    lbl_Phone2Value.text= [NSString stringWithFormat:@"%@",self.sitterInfo.sitterPhone2];
    lbl_emailValue.text=[NSString stringWithFormat:@"%@",self.sitterInfo.sitterEmail];
    if ([self.sitterInfo.sitterStatus isEqualToString:@"active"]) {
        [swSitterStatus setOn:YES];
    }else{
        [swSitterStatus setOn:NO];
    }
    lbl_aboutMe.text=[NSString stringWithFormat:@"About Me:"];
    [lbl_aboutMe setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lblSitterStatus setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lblSitterStatus setText:@"Active"];

    txtAboutMe.text=trimedString([NSString stringWithFormat:@"%@",self.sitterInfo.sitterAboutMe]);
    txtAboutMe.font=[UIFont fontWithName:Roboto_Regular size:FontSize12];
    [txtAboutMe setTextColor:kColorGrayLight];
    
    
    NSString *phone1= [NSString stringWithFormat:@" Phone 1:\t%@",self.sitterInfo.sitterPhone1];
    NSString *phone2= [NSString stringWithFormat:@" Phone 2:\t%@",self.sitterInfo.sitterPhone2];
    NSString *email=  [NSString stringWithFormat:@" Email:\t\t%@",self.sitterInfo.sitterEmail];
   //
    
    NSTextAttachment *iconPhone = [[NSTextAttachment alloc] init];// Create icon for phone1 lable
    iconPhone.image = [UIImage imageNamed:@"call.png"];
    iconPhone.bounds=CGRectMake(0,-3, 18, 18);
    // For phone 1
    NSMutableAttributedString *strPhone1= [[NSMutableAttributedString alloc]initWithString:phone1];
    NSMutableAttributedString *strPhone=(NSMutableAttributedString*)[NSMutableAttributedString attributedStringWithAttachment:iconPhone];
    NSRange range1 = [phone1 rangeOfString:@" Phone 1:"];
    [strPhone1 setAttributes:@{NSFontAttributeName:[UIFont fontWithName:Roboto_Medium size:FontSize12]}
                       range:range1];
    [strPhone1 setAttributes:@{NSFontAttributeName:[UIFont fontWithName:Roboto_Regular size:FontSize12]}
                       range:NSMakeRange(range1.length,phone1.length-range1.length)];
    
    [strPhone appendAttributedString:strPhone1];
    
    [lbl_Phone1Value setContentMode:UIViewContentModeTopLeft];
    lbl_Phone1Value.attributedText = strPhone;
    //For Phone2
    NSMutableAttributedString *strPhone2=(NSMutableAttributedString*)[NSMutableAttributedString attributedStringWithAttachment:iconPhone];
    NSMutableAttributedString *strUserPhone2= [[NSMutableAttributedString alloc]initWithString:phone2];
    
    [strUserPhone2 setAttributes:@{NSFontAttributeName:[UIFont fontWithName:Roboto_Medium size:FontSize12]}
                           range:range1];
    [strUserPhone2 setAttributes:@{NSFontAttributeName:[UIFont fontWithName:Roboto_Regular size:FontSize12]}
                           range:NSMakeRange(range1.length,phone2.length-range1.length)];
    [strPhone2 appendAttributedString:strUserPhone2];
    [lbl_Phone2Value setContentMode:UIViewContentModeTopLeft];
    lbl_Phone2Value.attributedText = strPhone2;
    
    //For email
    NSTextAttachment *iconEmail = [[NSTextAttachment alloc] init];
    iconEmail.image = [UIImage imageNamed:@"mail.png"];
    iconEmail.bounds=CGRectMake(0,-3, 18, 18);
    range1 = [email rangeOfString:@" Email:"];
    NSMutableAttributedString *strEmail=(NSMutableAttributedString*)[NSMutableAttributedString attributedStringWithAttachment:iconEmail];
    NSMutableAttributedString *strUserEmail= [[NSMutableAttributedString alloc]initWithString:email];
    [strUserEmail setAttributes:@{NSFontAttributeName:[UIFont fontWithName:Roboto_Medium size:FontSize12]}
                          range:range1];
    [strUserEmail setAttributes:@{NSFontAttributeName:[UIFont fontWithName:Roboto_Regular size:FontSize12]}
                          range:NSMakeRange(range1.length,email.length-range1.length)];
    [strEmail appendAttributedString:strUserEmail];
    [lbl_emailValue setContentMode:UIViewContentModeTopLeft];
    lbl_emailValue.attributedText = strEmail;
    lbl_emailValue.translatesAutoresizingMaskIntoConstraints=NO;
    if ([self.sitterInfo.sitterPhone2 isEqualToString:@""]) {
        
        [lbl_Phone2Value setAttributedText:strEmail];
        [lbl_emailValue setHidden:YES];
        [lbl_line4 setHidden:YES];
        
    }else{
        [lbl_emailValue setHidden:NO];
        [lbl_line4 setHidden:NO];
       
    }
    
}
-(void)setSitterStatus{
    if ([self.sitterInfo.sitterStatus isEqualToString:@"active"]) {
        [swSitterStatus setOn:YES];
    }else{
        [swSitterStatus setOn:NO];
    }
}
-(void)viewDidLayoutSubviews{
    [super viewDidLayoutSubviews];
   // view_mainBG.frame = CGRectMake(0, 0, self.view.bounds.size.width, MAX(504, self.view.bounds.size.height)  );
    
  }
- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark UITableViewDatasource

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView{
    return 5;
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (indexPath.section==0) {
        return 30;
    }
    return 25;
}

- (NSInteger)tableView:(UITableView *)table numberOfRowsInSection:(NSInteger)section
{
    if ([[arrayForSelectedSection objectAtIndex:section] boolValue]) {
        if (section==0) {
            if ([self.sitterInfo.str_OtherPreferences isEqualToString:@""]) {
                return [self.array_CertAndTraining count];
            }else{
                return [self.array_CertAndTraining count]+1;
            }
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
            cell.selectionStyle=UITableViewCellSelectionStyleNone;
            [cell setBackgroundColor:[UIColor clearColor]];
            cell.accessoryType=UITableViewCellAccessoryNone;
            [cell.lbl_certType setTextColor:kColorGrayDark];
            [cell.lbl_certValue setTextColor:kColorGrayDark];
            [cell.lbl_certType setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
            [cell.lbl_certValue setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];

            [cell.lbl_certType setText:@""];
            [cell.lbl_certValue setText:@""];
        }
        if (indexPath.row==self.array_CertAndTraining.count) {
            
            [cell.lbl_certType setHidden:YES];
            [cell.lbl_certValue setHidden:YES];
            UILabel *lblOtherCert=[[UILabel alloc]init];
            float xpos=cell.lbl_certType.frame.origin.x;
            CGRect lblFrm=CGRectMake(xpos+10,10, self.tblAdditionalInfo.frame.size.width, cell.lbl_certValue.frame.size.height);
            [lblOtherCert setFrame:lblFrm];
            [lblOtherCert setBackgroundColor:[UIColor clearColor]];
            [lblOtherCert setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
            [lblOtherCert setTextColor:kColorGrayDark];
            [lblOtherCert setText:[NSString stringWithFormat:@"%@ %@",kOtherCert, self.sitterInfo.str_OtherPreferences]];
            [cell.contentView addSubview:lblOtherCert];
        }else{
            [cell.lbl_certType setHidden:NO];
            [cell.lbl_certValue setHidden:NO];
            NSDictionary *d=[self.array_CertAndTraining objectAtIndex:indexPath.row];
            [cell.lbl_certType setText:[d valueForKey:kName]];
            if ([[d valueForKey:kDate] isEqualToString:@"NA"]) {
                [cell.lbl_certValue setText:[NSString stringWithFormat:@"%@",[[d valueForKey:kIsSelected] capitalizedString]]];
            }else{
                [cell.lbl_certValue setText:[NSString stringWithFormat:@"%@ %@",[[d valueForKey:kIsSelected] capitalizedString],[d valueForKey:kDate]]];
            }
        }
        return cell;
    }else if (indexPath.section==1) {
        if (otherCell==nil) {
            otherCell=[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:otherCellIdentifier];
            [otherCell.textLabel setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
            [otherCell.textLabel setTextColor:kColorGrayDark];
            otherCell.selectionStyle=UITableViewCellSelectionStyleNone;

        }
        NSDictionary *dictTemp=[self.array_Area safeObjectAtIndex:indexPath.row];
        otherCell.textLabel.text=[dictTemp objectForKey:kPreferName];
        return otherCell;
    }else if (indexPath.section==2) {
        if (otherCell==nil) {
            otherCell=[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:otherCellIdentifier];
            [otherCell.textLabel setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
            [otherCell.textLabel setTextColor:kColorGrayDark];
        }
        NSDictionary *dictTemp=[self.array_Language safeObjectAtIndex:indexPath.row];
        otherCell.textLabel.text=[dictTemp objectForKey:kPreferName];        return otherCell;
    }else if (indexPath.section==3) {
        if (otherCell==nil) {
            otherCell=[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:otherCellIdentifier];
            [otherCell.textLabel setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
            [otherCell.textLabel setTextColor:kColorGrayDark];
        }
        NSDictionary *dictTemp=[self.array_OtherPreferences safeObjectAtIndex:indexPath.row];
        otherCell.textLabel.text=[dictTemp objectForKey:kPreferName];        return otherCell;
    }else if (indexPath.section==4) {
        if (otherCell==nil) {
            otherCell=[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:otherCellIdentifier];
            [otherCell.textLabel setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
            [otherCell.textLabel setTextColor:kColorGrayDark];
        }
        NSDictionary *dictTemp=[self.array_ChildPreferences safeObjectAtIndex:indexPath.row];
        otherCell.textLabel.text=[dictTemp objectForKey:kPreferName];
        return otherCell;
    }
    return nil;
}

- (UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section{
    UIView *viewHeader=[[UIView alloc]initWithFrame:CGRectMake(0, 0,self.tblAdditionalInfo.frame.size.width , 40)];
    viewHeader.tag                  = section;
    [viewHeader setBackgroundColor:[UIColor clearColor]];
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
        strTitle= @"Other Information";
    }else{
        strTitle= @"Child Preferences";
    }
    [lblHeaderTitle setFont:[UIFont fontWithName:Roboto_Regular size:FontSize16]];
    [lblHeaderTitle setTextColor:kColorWhite];
    [lblHeaderTitle setText:strTitle];
    [viewHeader addSubview:lblHeaderTitle];
    
    UITapGestureRecognizer  *headerTapped   = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(sectionHeaderTapped:)];
    [viewHeader addGestureRecognizer:headerTapped];
    
    
    BOOL manyCells                  = [[arrayForSelectedSection objectAtIndex:section] boolValue];
    //up or down arrow depending on the bool
    UIImageView *upDownArrow        = [[UIImageView alloc] initWithImage:manyCells ? [UIImage imageNamed:@"accarwDown"]:[UIImage imageNamed:@"accarw"]];
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

#define mark -User Define method
- (IBAction)onClicked_editProfile:(id)sender {
    AdditionalInfoViewController *viewAddInfo=[[AdditionalInfoViewController alloc]initWithNibName:@"AdditionalInfoViewController" bundle:nil];
     self.navigationItem.title=@"";
    [self.navigationController pushViewController:viewAddInfo animated:YES];
}

- (IBAction)onClickedStatusChange:(id)sender {
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kUpdateSitterStatus_API];
    networkManager.delegate = self;
    NSMutableDictionary *dict_apiData=[[NSMutableDictionary alloc] init];
    [dict_apiData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [dict_apiData setSafeObject:self.sitterInfo.sitterId forKey:kUserId];
    [dict_apiData setSafeObject:self.sitterInfo.str_TokenData forKey:kToken];
    if (swSitterStatus.isOn) {
        //status / string / 'active' or 'inactive'
        [dict_apiData setSafeObject:@"active" forKey:kStatus];
    }else{
    [dict_apiData setSafeObject:@"inactive" forKey:kStatus];
    }
    [networkManager updateSitterStatus:dict_apiData forRequestNumber:1];
}

#pragma mark - SMCoreNetworkManagerDelegate

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                DDLogInfo(@"user data %@",dict_responseObj);
                self.sitterInfo.sitterStatus=[[[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kUserDetail] safeObjectForKey:kStatus];
                }
            else
            {
                [swSitterStatus setOn:NO];
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            break;
        default:
            break;
    }
    
    
    // [self.tbl_additionalInfo reloadData];
}

- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    // NSError *errorcode=(NSError *)error;
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}
@end


