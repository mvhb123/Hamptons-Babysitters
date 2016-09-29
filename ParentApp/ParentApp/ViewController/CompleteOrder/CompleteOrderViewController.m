//
//  CompleteOrderViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 17/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//
#import "JobListViewController.h"
#import "CompleteOrderViewController.h"
#import "BookingCreditsViewController.h"
#import "SitterRequirementViewController.h"
#import "ChildProfileImages.h"
#import "HomePageViewController.h"
#import "RequestSitterViewController.h"

@interface CompleteOrderViewController ()

@end

@implementation CompleteOrderViewController
@synthesize dict_addJobRequirement;

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.navigationItem.title = @"Complete Order";
    NSString *str_childCount = [NSString stringWithFormat:@"%lu",(unsigned long)[ApplicationManager getInstance].array_selectedChild.count];
    
    [dict_addJobRequirement setSafeObject:str_childCount forKey:kChildCount];
    [self.backgroundScrollView setHidden:YES];
    [self bookinFeesJobSummaryAPI];
    DDLogInfo(@"Child is  %@",[ApplicationManager getInstance].array_selectedChild);
    NSMutableArray *array_childId = [[NSMutableArray alloc]init];
    for (int i=0; i<[ApplicationManager getInstance].array_selectedChild.count; i++) {
        childrenInfo = [[ApplicationManager getInstance].array_selectedChild safeObjectAtIndex:i];
        [array_childId addObject:childrenInfo.childJobId];
    }
    str_childId = [array_childId componentsJoinedByString:@","];
    [self setFontTypeAndFontSize];
    [self setDataInView];
    [self setChildInView];
    
    checkValue = 0;
    self.view.backgroundColor=kViewBackGroundColor;
    tbl_sitterRequirement.backgroundColor = kViewBackGroundColor;
    
    
}
-(void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:YES];
    DDLogInfo(@"add job value is %@",dict_addJobRequirement);
    
}
-(void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    if (array_preferences.count==0) {
        [tbl_sitterRequirement setFrame:CGRectMake(tbl_sitterRequirement.frame.origin.x,tbl_sitterRequirement.frame.origin.y,tbl_sitterRequirement.frame.size.width,0)];
    }
    [txtView_specialNeeds.layer setBorderColor:[[[UIColor grayColor] colorWithAlphaComponent:0.5] CGColor]];
    [txtView_specialNeeds.layer setBorderWidth:1.0];
    txtView_specialNeeds.clipsToBounds = YES;
    [txtViewSpecialInstructions.layer setBorderColor:[[[UIColor grayColor] colorWithAlphaComponent:0.5] CGColor]];
    [txtViewSpecialInstructions.layer setBorderWidth:1.0];
    txtViewSpecialInstructions.clipsToBounds = YES;
}
-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    [self setFontTypeAndFontSize];
    self.backgroundScrollView.translatesAutoresizingMaskIntoConstraints=YES;
    contentHight = btn_buyMultipleCredits.frame.origin.y+btn_buyMultipleCredits.frame.size.height+view_addJobDetail.frame.origin.y;
//    UIView *lLast = view_addJobDetail;
//    NSInteger wd = lLast.frame.origin.y;
//    NSInteger ht = lLast.frame.size.height;
//    contentHight = wd+ht;
    if ([txtView_specialNeeds.text isEqualToString:@""]) {
        [lbl_SpecialNeedsHeading setHidden:YES];
        [txtView_specialNeeds setHidden:YES];
        if (![txtViewSpecialInstructions.text isEqualToString:@""]){
            [lbl_specialInsHeading setHidden:NO];
            [txtViewSpecialInstructions setHidden:NO];
            lbl_specialInsHeading.frame=lbl_SpecialNeedsHeading.frame;
            txtViewSpecialInstructions.frame=txtView_specialNeeds.frame;
            contentHight=contentHight+100;
            if (array_preferences.count!=0){
                CGRect lblFrm=lbl_sitterReqHeading.frame;
                lblFrm.origin.y=txtViewSpecialInstructions.frame.origin.y+txtViewSpecialInstructions.frame.size.height;
                lbl_sitterReqHeading.frame=lblFrm;
                CGRect tblFrm=tbl_sitterRequirement.frame;
                tblFrm.origin.y=lbl_sitterReqHeading.frame.origin.y+lbl_sitterReqHeading.frame.size.height;
                tbl_sitterRequirement.frame=tblFrm;
                contentHight=contentHight+tbl_sitterRequirement.frame.size.height+lbl_sitterReqHeading.frame.size.height;
            }
        }else if (array_preferences.count!=0){
            [lbl_specialInsHeading setHidden:YES];
            [txtViewSpecialInstructions setHidden:YES];
            CGRect lblFrm=lbl_sitterReqHeading.frame;
            lblFrm.origin.y=lbl_SpecialNeedsHeading.frame.origin.y;
            lbl_sitterReqHeading.frame=lblFrm;
            CGRect tblFrm=tbl_sitterRequirement.frame;
            tblFrm.origin.y=lbl_sitterReqHeading.frame.origin.y+lbl_sitterReqHeading.frame.size.height;
            tbl_sitterRequirement.frame=tblFrm;
            contentHight=contentHight+tbl_sitterRequirement.frame.size.height+lbl_sitterReqHeading.frame.size.height;
        }
        
    }else{
        [lbl_SpecialNeedsHeading setHidden:NO];
        [txtView_specialNeeds setHidden:NO];
        contentHight=contentHight+100;
        if (![txtViewSpecialInstructions.text isEqualToString:@""]){
            contentHight=contentHight+100;
            [lbl_specialInsHeading setHidden:NO];
            [txtViewSpecialInstructions setHidden:NO];
            if (array_preferences.count!=0){
                CGRect lblFrm=lbl_sitterReqHeading.frame;
                lblFrm.origin.y=txtViewSpecialInstructions.frame.origin.y+txtViewSpecialInstructions.frame.size.height;
                lbl_sitterReqHeading.frame=lblFrm;
                CGRect tblFrm=tbl_sitterRequirement.frame;
                tblFrm.origin.y=lbl_sitterReqHeading.frame.origin.y+lbl_sitterReqHeading.frame.size.height;
                tbl_sitterRequirement.frame=tblFrm;
                contentHight=contentHight+tbl_sitterRequirement.frame.size.height+lbl_sitterReqHeading.frame.size.height;
            }
        }else if (array_preferences.count!=0){
            [lbl_specialInsHeading setHidden:YES];
            [txtViewSpecialInstructions setHidden:YES];
            CGRect lblFrm=lbl_sitterReqHeading.frame;
            lblFrm.origin.y=txtView_specialNeeds.frame.origin.y+txtView_specialNeeds.frame.size.height;
            lbl_sitterReqHeading.frame=lblFrm;
            CGRect tblFrm=tbl_sitterRequirement.frame;
            tblFrm.origin.y=lbl_sitterReqHeading.frame.origin.y+lbl_sitterReqHeading.frame.size.height;
            tbl_sitterRequirement.frame=tblFrm;
            contentHight=contentHight+tbl_sitterRequirement.frame.size.height+lbl_sitterReqHeading.frame.size.height;
        }else{
            [lbl_specialInsHeading setHidden:YES];
            [txtViewSpecialInstructions setHidden:YES];
        }

    
    }

    if (array_preferences.count==0) {
                [lbl_sitterReqHeading setHidden:YES];
                [tbl_sitterRequirement setHidden:YES];
    }
    self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight);
    DDLogInfo(@">>>> %f  ht %f",contentHight,self.backgroundScrollView.frame.size.height);
    [self.view layoutIfNeeded];
}
- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
#pragma mark-UserDefineMethods
-(void)setFontTypeAndFontSize
{   // set font type and size
    
    lbl_selectedChildren.font=[UIFont fontWithName:RobotoMediumFont size:ButtonFieldFontSize];
    lbl_bottomLabelMessage.font=[UIFont fontWithName:RobotoRegularFont size:11];
    lbl_dateTimeHeading.font=[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_startDTHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_endDTHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_jobAddressHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_feeSuumaryHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    
    lbl_babySitterFeeHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_babySitterFees.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    
    lbl_bookingFeeHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_bookingFees.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    
    
    lbl_availableCredits.font =  [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_availbaleCreditsValue.font =  [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    
    lbl_totalChargeHeading.font =  [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_totalCharge.font =  [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    
    
    lbl_specialInsHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_SpecialNeedsHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_sitterReqHeading.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_startDateTime.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_endDateTime.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    llbl_jobAddress.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    [txtViewSpecialInstructions setFont:[UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize]];
    txtViewSpecialInstructions.backgroundColor = kViewBackGroundColor;
    txtView_specialNeeds.backgroundColor = kViewBackGroundColor;
    [txtView_specialNeeds setContentInset:UIEdgeInsetsMake(-8, 0, 0, 0)];
    [txtViewSpecialInstructions setContentInset:UIEdgeInsetsMake(-8, 0, 0, 0)];
    btn_buyMultipleCredits.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    btn_completeOrder.titleLabel.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    NSMutableAttributedString *string = [[NSMutableAttributedString alloc] initWithString:btn_buyMultipleCredits.titleLabel.text];
    [string addAttribute:NSUnderlineStyleAttributeName value:[NSNumber numberWithInt:1] range:(NSRange){0,[string length]}];
    btn_buyMultipleCredits.titleLabel.attributedText = string;
    
    [txtViewSpecialInstructions setTextColor:kLabelColor];
    [txtView_specialNeeds setTextColor:kLabelColor];
    
    [lbl_selectedChildren setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_bottomLabelMessage setTextColor:kLabelColor];
    [lbl_dateTimeHeading setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_startDTHeading setTextColor:kLabelColor];
    [lbl_endDTHeading setTextColor:kLabelColor];
    [lbl_jobAddressHeading setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_feeSuumaryHeading setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_SpecialNeedsHeading setTextColor:UIColorFromHexCode(0x04005c)];
    
    [lbl_babySitterFeeHeading setTextColor:kLabelColor];
    [lbl_babySitterFees setTextColor:kLabelColor];
    [lbl_bookingFees setTextColor:kLabelColor];
    [lbl_bookingFeeHeading setTextColor:kLabelColor];
    [lbl_availableCredits setTextColor:kLabelColor];
    [lbl_availbaleCreditsValue setTextColor:kLabelColor];
    [lbl_specialInsHeading setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_sitterReqHeading setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_startDateTime setTextColor:kLabelColor];
    [lbl_endDateTime setTextColor:kLabelColor];
    [llbl_jobAddress setTextColor:kLabelColor];
    [lbl_totalCharge setTextColor:kLabelColor];
    [lbl_totalChargeHeading setTextColor:kLabelColor];
    
    
}
-(void)setChildInView
{
    int j=10;
    NSArray *viewsToRemove = [view_scrollView subviews];
    for (ChildProfileImages *v in viewsToRemove)
        [v removeFromSuperview];
    
    for (int i=0; i<[ApplicationManager getInstance].array_selectedChild.count; i++)
    {
        ChildProfileImages *childProfile;
        childrenInfo = [[ApplicationManager getInstance].array_selectedChild safeObjectAtIndex:i];
        NSArray *nibArray = [[NSBundle mainBundle] loadNibNamed:@"ChildProfileImages" owner:self options:nil];
        childProfile = [nibArray safeObjectAtIndex:0];
        
        [childProfile setFrame:CGRectMake(j,4, childProfile.frame.size.width, childProfile.frame.size.height)];
        j=j+childProfile.frame.size.width;
        childProfile.lbl_childName.text = childrenInfo.childName;
        childProfile.btn_selectImage.tag = i;
        childProfile.btn_selectImage.hidden = true;
        childProfile.img_checkedChild.tag = i;
        NSURL *img_url=[NSURL URLWithString:childrenInfo.childThumbImage];
        [childProfile.view_childImage loadImageFromURL:img_url];
        //childProfile.view_childImage.layer.cornerRadius = childProfile.view_childImage.frame.size.width/2;
        // childProfile.view_childImage.clipsToBounds = YES;
        
        [view_scrollView addSubview:childProfile];
        
    }
    view_scrollView.contentSize = CGSizeMake(j,112);
}
-(void)setDataInView
{
    array_lanaguge = [[NSMutableArray alloc]init];
    array_otherPreference = [[NSMutableArray alloc]init];
    NSMutableArray *array_preferenceId = [[NSMutableArray alloc]init];
    lbl_startDateTime.text = [dict_addJobRequirement objectForKey:kJobStartDate];
    lbl_endDateTime.text = [dict_addJobRequirement objectForKey:kJobEndDate];
    NSString *str_localAddress = [NSString stringWithFormat:@""];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[[dict_addJobRequirement objectForKey:kLocalAddress]objectForKey:kStreetAddress]]];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[[dict_addJobRequirement objectForKey:kLocalAddress]objectForKey:kCity]]];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[[dict_addJobRequirement objectForKey:kLocalAddress]objectForKey:kState]]];
    str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@ ",[[dict_addJobRequirement objectForKey:kLocalAddress]objectForKey:kZip]]];
    if (![[[dict_addJobRequirement objectForKey:kLocalAddress]objectForKey:kCrossStreet] isEqualToString:@""]) {
        str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[[dict_addJobRequirement objectForKey:kLocalAddress]objectForKey:kCrossStreet]]];
    }
    if (![[[dict_addJobRequirement objectForKey:kLocalAddress]objectForKey:kHotelName] isEqualToString:@""]) {
        str_localAddress = [str_localAddress stringByAppendingString:[NSString stringWithFormat:@"%@",[[dict_addJobRequirement objectForKey:kLocalAddress]objectForKey:kHotelName]]];
    }
    llbl_jobAddress.text = str_localAddress;
    txtViewSpecialInstructions.text = [dict_addJobRequirement objectForKey:kChildNotes];
    txtView_specialNeeds.text = [dict_addJobRequirement objectForKey:@"special_need"];
    array_preferences = [[NSMutableArray alloc]init];
    array_preferences = [dict_addJobRequirement objectForKey:kPreferences];
    for (int i=0;i<array_preferences.count; i++) {
        [array_preferenceId addObject:[[array_preferences objectAtIndex:i]objectForKey:kPreferId]];
        if ([[[array_preferences objectAtIndex:i]objectForKey:kGroupName]isEqualToString:kLanguage]) {
            [array_lanaguge addObject:[[array_preferences objectAtIndex:i]objectForKey:kPreferName]];
        }
        else
            [array_otherPreference addObject:[[array_preferences objectAtIndex:i]objectForKey:kPreferName]];
        
    }
    str_preferenceId = [array_preferenceId componentsJoinedByString:@","];
    //     lbl_langauges.text = [array_lanaguge  componentsJoinedByString:@","];
    //     lbl_otherPrefrences.text = [array_otherPreference componentsJoinedByString:@","];
    [tbl_sitterRequirement reloadData];
}
-(void)bookinFeesJobSummaryAPI
{
    NSDate *currentdate = [NSDate date];
    NSDateFormatter *dateformate=[[NSDateFormatter alloc]init];
    [dateformate setDateFormat:@"yyyy-MM-dd HH:mm:ss"];
    NSString *str_currentDate = [dateformate stringFromDate:currentdate];
    [dict_addJobRequirement setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
    [dict_addJobRequirement setSafeObject:self.parentInfo.tokenData forKey:kToken];
    [dict_addJobRequirement setSafeObject:str_currentDate forKey:kCurrentdate];
    [self startNetworkActivity:YES];
    [dict_addJobRequirement setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kBookingFeeJobSummaryAPI];
    DDLogInfo(@"%@",kBookingFeeJobSummaryAPI);
    networkManager.delegate = self;
    [networkManager bookinFee:dict_addJobRequirement forRequestNumber:1];
}
- (IBAction)onClickBookingCredits:(id)sender {
    checkValue = 1;
    BookingCreditsViewController *bookingCredits = [[BookingCreditsViewController alloc]initWithNibName:@"BookingCreditsViewController" bundle:nil];
    [self.navigationController pushViewController:bookingCredits animated:YES];
    
}
- (IBAction)onClickCompleteOrder:(id)sender {
    NSMutableDictionary *dict_addjobData = [[NSMutableDictionary alloc]init];
    NSDate *currentdate = [NSDate date];
    //Convert into New york time
    NSDateFormatter *dateFormatForSystem=[[NSDateFormatter alloc]init];
    [dateFormatForSystem setTimeZone:[NSTimeZone systemTimeZone]];
    [dateFormatForSystem setDateFormat:@"yyyy-MM-dd HH:mm:ss"];
    NSString *str_currentDate = [dateFormatForSystem stringFromDate:currentdate];
    NSLog(@"system time  --   %@",str_currentDate);
    NSDateFormatter *dateFormatForUTC = [[NSDateFormatter alloc] init];
    dateFormatForUTC.dateFormat = @"yyyy-MM-dd HH:mm:ss";
    
    NSTimeZone *gmt = [NSTimeZone timeZoneWithAbbreviation:@"UTC"];
    [dateFormatForUTC setTimeZone:gmt];
    NSString *utcTime = [dateFormatForUTC stringFromDate:[dateFormatForSystem dateFromString:str_currentDate]];
    NSLog(@"UTC -- %@",utcTime);
    NSDateFormatter *dateFormatForEDT = [[NSDateFormatter alloc] init];
    dateFormatForEDT.dateFormat = @"yyyy-MM-dd HH:mm:ss";
    NSTimeZone *edt = [NSTimeZone timeZoneWithAbbreviation:@"EDT"];
    [dateFormatForEDT setTimeZone:edt];
    NSString *edtTime = [dateFormatForEDT stringFromDate:[dateFormatForUTC dateFromString:utcTime]];
    
    NSLog(@"EDT -- %@",edtTime);
    
    [dict_addjobData setSafeObject:edtTime forKey:kCurrentdate];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:kJobStartDate] forKey:kJobStartDate];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:kJobEndDate] forKey:kJobEndDate];
    [dict_addjobData setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [dict_addjobData setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
    [dict_addjobData setSafeObject:self.parentInfo.tokenData forKey:kToken];
    [dict_addjobData setSafeObject:str_preferenceId forKey:kPreferences];
    [dict_addjobData setSafeObject:str_childId forKey:kChildId];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:kCardInfo] forKey:kCardInfo];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:kNameOnCard] forKey:kNameOnCard];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:kSaveCard] forKey:kSaveCard];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:kChildNotes] forKey:kNotes];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:kPackageId] forKey:kPackageId];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:kStreetAddress] forKey:kStreetAddress];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:kState] forKey:kState];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:kCity] forKey:kCity];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:kZip] forKey:kZip];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:kisSpecialNeed] forKey:kisSpecialNeed];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:@"special_need"] forKey:@"special_need"];
    [dict_addjobData setSafeObject:[dict_addJobRequirement safeObjectForKey:kAutherizePaymentId] forKey:kAutherizePaymentId];
    if ([[[dict_addJobRequirement safeObjectForKey:kLocalAddress] allKeys]containsObject:kAddressId]) {
        [dict_addjobData setSafeObject:[[dict_addJobRequirement safeObjectForKey:kLocalAddress] safeObjectForKey:kAddressId]forKey:kAddressId];
        
    }
    else
    {
        [dict_addJobRequirement setSafeObject:[[dict_addJobRequirement safeObjectForKey:kLocalAddress]safeObjectForKey:kStateId] forKey:[[dict_addJobRequirement safeObjectForKey:kLocalAddress]safeObjectForKey:kState]];
        NSData *data = [NSJSONSerialization dataWithJSONObject:[dict_addJobRequirement safeObjectForKey:kLocalAddress] options:NSJSONWritingPrettyPrinted error:nil];
        NSString *str_localAddress = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
        [dict_addjobData setSafeObject:str_localAddress forKey:kAlternateAddress];
    }
    
    DDLogInfo(@"complete order value is %@",dict_addjobData);
    if (![[ApplicationManager getInstance]isDeviceProxyIsEnabled]) {
        [self startNetworkActivity:NO];
        SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kAddJobAPI];
        DDLogInfo(@"%@",kAddJobAPI);
        networkManager.delegate = self;
        [networkManager addJobRequest:dict_addjobData forRequestNumber:2];
    }
    else{
        [[ApplicationManager getInstance]showAlertForVC:nil withTitle:nil andMessage:kProxyServer];
    }
    
}
#pragma mark UITableViewDatasource

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView{
    return 2;
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 20;
}

- (NSInteger)tableView:(UITableView *)table numberOfRowsInSection:(NSInteger)section
{
    if (section==0) {
        return array_lanaguge.count;
    }
    if (section == 1) {
        return array_otherPreference.count;
    }
    
    return 0;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *simpleTableIdentifier = @"SimpleTableItem";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:simpleTableIdentifier];
    if (cell == nil) {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:simpleTableIdentifier];
    }
    cell.selectionStyle = UITableViewCellSelectionStyleNone;
    cell.backgroundColor = kViewBackGroundColor;
    cell.textLabel.textColor = kColorGrayDark;
    cell.textLabel.font=[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    if (indexPath.section==0) {
        cell.textLabel.text = [array_lanaguge objectAtIndex:indexPath.row];
        return cell;
    }
    if (indexPath.section==1) {
        cell.textLabel.text =[array_otherPreference objectAtIndex:indexPath.row];
        
        return cell;
    }
    return nil;
}

- (UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section{
    UIView *viewHeader=[[UIView alloc]initWithFrame:CGRectMake(0,0,tbl_sitterRequirement.frame.size.width ,21)];
    [viewHeader setBackgroundColor:kViewBackGroundColor];
    CGRect lblFrm=viewHeader.frame;
    UILabel *lblHeaderTitle=[[UILabel alloc]initWithFrame:lblFrm];
    
    NSString *strTitle=@"";
    if (section==0) {
        if (array_lanaguge.count>0) {
            strTitle= @"  Langauges";
        }else{
            strTitle= @"";
        }
        
    }else{
        if (array_otherPreference.count>0) {
            strTitle= @"  Other Preferences";
        }else{
            strTitle= @"";
        }
        
    }
    [lblHeaderTitle setTextColor:UIColorFromHexCode(0x04005c)];
    [lblHeaderTitle setText:strTitle];
    lblHeaderTitle.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    [viewHeader addSubview:lblHeaderTitle];
    return viewHeader;
}

#pragma mark - SMCoreNetworkManagerDelegate

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    [self.backgroundScrollView setHidden:NO];
    DDLogInfo(@"%@",responseObject);
    switch (requestId) {
        case 1: // for fee summary.
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                
                [ApplicationManager getInstance].parentInfo.tokenData = [[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kTokenData];
                lbl_babySitterFees.text = [[responseObject safeObjectForKey:kData]safeObjectForKey:kBookinFees];
                lbl_bookingFees.text = [[responseObject safeObjectForKey:kData]safeObjectForKey:kcreditsUsed];
                [lbl_bookingFees sizeToFit];
                CGRect frm=view_feeSumarry.frame;
                    frm.origin.x=8;
                    frm.origin.y=lbl_bookingFees.frame.origin.y+lbl_bookingFees.frame.size.height+1;
                frm.size.width=kScreenWidth-16;
                frm.size.height=40;
                [view_feeSumarry setFrame:frm];
                lbl_availbaleCreditsValue.text = [[[responseObject safeObjectForKey:kData]safeObjectForKey:@"remaining_credits"] stringValue];
                lbl_totalCharge.text=[[responseObject safeObjectForKey:kData]safeObjectForKey:@"total_charged"];
                
               // NSArray* array_BookingFees = [lbl_bookingFees.text componentsSeparatedByString:@" "];
                NSString* str_numericValue = [[[responseObject safeObjectForKey:kData]safeObjectForKey:@"required_credits"] stringValue];
                str_numericValue=[str_numericValue stringByReplacingOccurrencesOfString:@"$" withString:@""];
                int numValue = [str_numericValue intValue];
                if (numValue <= [[[responseObject safeObjectForKey:kData]safeObjectForKey:kAvailableCredits]intValue]) {
                    btn_buyMultipleCredits.hidden = true;
                    //btn_buyMultipleCredits.enabled=NO;
                }
                
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                NSArray *array = [self.navigationController viewControllers];
                for (int i=0;i<array.count;i++) {
                    if ([[array objectAtIndex:i] isKindOfClass:[RequestSitterViewController class]])
                    {
                        [self.navigationController popToViewController:[array objectAtIndex:i] animated:YES];
                        break;
                        
                    }
                    
                }
                
            }
            
            break;
        case 2: // complete order.
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[dict_responseObj valueForKey:kMessage]];
                [ApplicationManager getInstance].parentInfo.tokenData = [[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kTokenData];
                [ApplicationManager getInstance].parentInfo.authrizedPaymentProfileId = [[[responseObject safeObjectForKey:kData]safeObjectForKey:kJDetail]safeObjectForKey:kAutherizePaymentId];
                
                NSArray *array = [self.navigationController viewControllers];
                for (int i=0;i<array.count;i++) {
                    if ([[array objectAtIndex:i] isKindOfClass:[HomePageViewController class]])
                    {
                        [self.navigationController popToViewController:[array objectAtIndex:i] animated:YES];
                        break;
                        
                    }
                    
                }
                
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            
            break;
            
        case 6:// for logout
            [self logout:dict_responseObj];
            break;
            
            
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    [self.backgroundScrollView setHidden:NO];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
}

@end
