






//
//  JobDetailViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 16/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "JobDetailViewController.h"
#import "SitterProfileViewController.h"
#import "JobListViewController.h"
#import "KidsListTableViewCell.h"
#import "ProfileViewController.h"
#import "AddChildView.h"

@interface JobDetailViewController ()

@end

@implementation JobDetailViewController
@synthesize dict_jobsDetail;

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    array_childList = [[NSMutableArray alloc]init];
    scrollView_kidsList.pagingEnabled = YES;
    [self setFontTypeAndFontSize];
    self.numFormatter = [[NumberFormatter alloc] initWithRegionCode:@"US"];
    if (self.checkJobType ==1) {
        self.navigationItem.title = @"Active Job Detail";
        //self.btn_cancel.hidden = true;
    }
    else
        self.navigationItem.title = @"Job Detail";
    btn_sitterName.hidden = true;
    lbl_babySitterEmailHeader.hidden = true;
    lbl_babySitterNameHeader.hidden = true;
    lbl_babySitterPhoneHeader.hidden = true;
    img_sitterProfile.hidden=true;
    lbl_sitterName.hidden = true;
    lbl_sitterDetail.hidden = true;
    lbl_sitterEmail.hidden = true;
    lbl_sitterMobileNumber.hidden = true;

    self.parentInfo = [ApplicationManager getInstance].parentInfo;
    [dict_jobsDetail setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
    [dict_jobsDetail setSafeObject:self.parentInfo.tokenData forKey:kToken];
    [dict_jobsDetail setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    DDLogInfo(@"dict value is %@",dict_jobsDetail);
    [self startNetworkActivity:YES];
    SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kJobDetailAPI];
    DDLogInfo(@"%@",kJobDetailAPI);
    networkManager.delegate = self;
    [networkManager JobsDetail:self.dict_jobsDetail forRequestNumber:2];
    contentHight = 0;
    UIView *lLast = [view_jobDetail.subviews lastObject];
    NSInteger wd = lLast.frame.origin.y;
    NSInteger ht = lLast.frame.size.height;
    contentHight = wd+ht;
    if (lbl_sitterDetail.hidden == true) {
        self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+10);
    }
    
    
    else
    {
        self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+10);
    }

    [self tapEmailAndPhoneGesture];
   // tbl_kidsList.translatesAutoresizingMaskIntoConstraints = YES;
    pageNo=0;
    j=0;
    self.view.backgroundColor=kViewBackGroundColor;

}
-(void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
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
    UIView *lLast = [view_jobDetail.subviews lastObject];
    NSInteger wd = lLast.frame.origin.y;
    NSInteger ht = lLast.frame.size.height;
    contentHight = wd+ht;
    if (lbl_sitterDetail.hidden == true) {
        self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+10);
   }
   else
   {
    self.backgroundScrollView.contentSize = CGSizeMake(self.backgroundScrollView.frame.size.width,contentHight+10);
   }
}

#pragma mark-UserDefineMethods
-(void)setFontTypeAndFontSize
{
    lbl_jobNumber.font=[UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_startDate.font=[UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_enddate.font=[UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_status.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_sitterName.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_sitterEmail.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_sitterMobileNumber.font = [UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];
    lbl_viewJobNumber.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_address.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_viewStartDate.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_viewEndDate.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_viewStatus.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_babySitterNameHeader.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_babySitterEmailHeader.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_babySitterPhoneHeader.font = [UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize];
    lbl_sitterDetail.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    lbl_kidsListHeading.font = [UIFont fontWithName:RobotoRegularFont size:ButtonFieldFontSize];
    lbl_jobAddress.font=[UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize];

    [lbl_kidsListHeading setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_sitterDetail setTextColor:UIColorFromHexCode(0x04005c)];
    [lbl_jobNumber setTextColor:kLabelColor];
    [lbl_startDate setTextColor:kLabelColor];
    [lbl_enddate setTextColor:kLabelColor];
    [lbl_status setTextColor:kLabelColor];
    [lbl_sitterName setTextColor:kLabelColor];
    [lbl_sitterEmail setTextColor:kLabelColor];
    [lbl_sitterMobileNumber setTextColor:kLabelColor];
    [lbl_viewJobNumber setTextColor:kLabelColor];
    [lbl_viewStartDate setTextColor:kLabelColor];
    [lbl_viewEndDate setTextColor:kLabelColor];
    [lbl_viewStatus setTextColor:kLabelColor];
    [lbl_babySitterNameHeader setTextColor:kLabelColor];
    [lbl_babySitterEmailHeader setTextColor:kLabelColor];
    [lbl_babySitterPhoneHeader setTextColor:kLabelColor];
    [lbl_jobAddress setTextColor:kLabelColor];
    [lbl_address setTextColor:kLabelColor];

    
}
- (IBAction)onClickCancelJOb:(id)sender {
    DDLogInfo(@"job start date %@",self.jobInfo.jobStartDate);
    DDLogInfo(@"current  date %@",[NSDate date]);
    NSDateFormatter *dateFormatForSystem=[[NSDateFormatter alloc]init];
    [dateFormatForSystem setTimeZone:[NSTimeZone systemTimeZone]];
    [dateFormatForSystem setDateFormat:@"MMM dd, yyyy hh:mm a"];
    NSDate *str_jobStartDate = [dateFormatForSystem dateFromString:self.jobInfo.jobStartDate];
    NSString *strcurrentDate=[dateFormatForSystem stringFromDate:[NSDate date]];
     NSDate *str_currentDate = [dateFormatForSystem dateFromString:strcurrentDate];
    //str_currentDate =[NSDate ]
    DDLogInfo(@"After convert job start date %@",[self getEdtDate:str_jobStartDate]);
    DDLogInfo(@"After convert current  date %@",[self getEdtDate:str_currentDate]);
    [dateFormatForSystem setDateFormat:@"yyyy-MM-dd HH:mm:ss"];
    NSDate *date1 = [dateFormatForSystem dateFromString:[self getEdtDate:str_jobStartDate]];
    NSDate *date2 = [dateFormatForSystem dateFromString:[self getEdtDate:str_currentDate]];
    
    NSTimeInterval secondsBetween = [date1 timeIntervalSinceDate:date2];
    
    int numberOfDays = secondsBetween / 86400;
   // int hrs=numberOfDays/24;
    //NSLog(@"There are %d days in between the two dates.", numberOfDays);
    //Cancel Job, Do Not Cancel Job
    if (numberOfDays<=0) {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:@"Cancellations inside 24 hours before the job start will be billed the minimum 3 hour engagement. You keep the booking credit for future use." delegate:self cancelButtonTitle:@"Cancel Job" otherButtonTitles:@"Do Not Cancel Job", nil];
        [alert setTag:100];
        [alert show];
    }else{
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:kConfirmationMsgForCancelJob delegate:self cancelButtonTitle:@"Cancel Job" otherButtonTitles:@"Do Not Cancel Job", nil];
        [alert setTag:100];
        [alert show];
    }
    
}

- (IBAction)onClickSitterProfile:(id)sender {
    ProfileViewController *sitterProfile = [[ProfileViewController alloc]initWithNibName:@"ProfileViewController" bundle:nil];
    sitterProfile.str_sitterId =self.jobInfo.sitterUserId;
    [self.navigationController pushViewController:sitterProfile animated:YES];
}

- (IBAction)onClickNext:(id)sender {
    if (pageNo < totalCount) {
    btn_next.hidden = false;
    btn_previous.hidden = true;
    pageNo++;
    [scrollView_kidsList setContentOffset:CGPointMake(scrollView_kidsList.frame.size.width*pageNo, scrollView_kidsList.contentOffset.y) animated:YES];
        if (pageNo == totalCount) {
            btn_next.hidden = false;
        }
    }
    else
        btn_next.hidden = false;
}

- (IBAction)onClickPrevious:(id)sender {
    if (pageNo > 0) {
    btn_previous.hidden = false;
    btn_next.hidden = false;
    
    pageNo--;
    [scrollView_kidsList setContentOffset:CGPointMake(scrollView_kidsList.frame.size.width*pageNo, scrollView_kidsList.contentOffset.y) animated:YES];
         DDLogInfo(@"page no %d and contentOffset %f",pageNo,scrollView_kidsList.contentOffset.x);
        if (pageNo == 0) {
            btn_previous.hidden = true;
        }
   
    }
    else
        btn_previous.hidden = true;
        
}
- (void)scrollViewDidScroll:(UIScrollView *)scrollView {
    if (((int)scrollView.contentOffset.x % (int)scrollView.frame.size.width) == 0) {
        currentPage = scrollView.contentOffset.x /scrollView.frame.size.width;
        pageNo = currentPage;
        if (pageNo == 0) {
            btn_previous.hidden = true;
            btn_next.hidden = false;
        }
        else
            btn_previous.hidden = false;
        if (pageNo==totalCount) {
            btn_next.hidden = true;
            btn_previous.hidden = false;
        }
        else
            btn_next.hidden = false;
        if (array_childList.count == 1) {
            btn_next.hidden = true;
            btn_previous.hidden = true;
        }
    }
   
}
-(void)setDataInView
{
    [lbl_jobAddress setVerticalAlignment:VerticalAlignmentTop];
    lbl_viewJobNumber.text = @"Job Number";
    lbl_viewStartDate.text = @"Start Date";
    lbl_viewEndDate.text = @"End Date";
    lbl_viewStatus.text = @"Status";
    lbl_kidsList.text = @"Kids List";
    self.jobInfo = [ApplicationManager getInstance].jobDetail;
    lbl_jobNumber.text = self.jobInfo.jobId;
    lbl_status.text =    self.jobInfo.jobStatus;
    lbl_jobAddress.text=self.jobInfo.jobAddress;
    lbl_startDate.text = [self convertDateFormate:self.jobInfo.jobStartDate andDateFormate:@"MMM dd, yyyy hh:mm a"];
    lbl_enddate.text =   [self convertDateFormate:self.jobInfo.jobEndDate andDateFormate:@"MMM dd, yyyy hh:mm a"];
    if ([self.jobInfo.jobStatus isEqualToString:@"completed"]) {
        self.btn_cancel.hidden = true;
    }
    if ([self.jobInfo.jobStatus isEqualToString:@"cancelled"]) {
        self.btn_cancel.hidden = true;
    }
    if (![self.jobInfo.sitterUserId isEqualToString:@""]) {
        btn_sitterName.hidden = false;
        lbl_babySitterEmailHeader.hidden = false;
        lbl_babySitterNameHeader.hidden = false;
        lbl_babySitterPhoneHeader.hidden = false;
        lbl_sitterMobileNumber.hidden = false;
        img_sitterProfile.hidden=false;
        lbl_sitterEmail.hidden = false;
        lbl_sitterName.hidden = false;
        lbl_sitterDetail.hidden = false;
        lbl_sitterName.text = [NSString stringWithFormat:@"%@ %@",self.jobInfo.sitterFirstName,self.jobInfo.sitterLastName];
        lbl_sitterEmail.text = self.jobInfo.sitterUserName;
        lbl_sitterMobileNumber.text =[self.numFormatter formatText:self.jobInfo.sitterPhone];
        [img_sitterProfile loadImageFromURL:[NSURL URLWithString:self.jobInfo.sitterProfilePic]];
        NSMutableAttributedString* attrString = [[NSMutableAttributedString alloc] initWithString:lbl_sitterMobileNumber.text];
        [attrString addAttribute:NSUnderlineStyleAttributeName
                           value:[NSNumber numberWithInt:1]
                           range:(NSRange){0,[attrString length]}];
        lbl_sitterMobileNumber.attributedText = [attrString mutableCopy];
    }
    else
         btn_sitterName.hidden = true;
}
-(void)setDataInScrollView
{
    if (array_childList.count == 1) {
        btn_next.hidden = true;
    }
    btn_previous.hidden = true;
    DDLogInfo(@"array children detail %@",array_childList);
    NSArray *viewsToRemove = [scrollView_kidsList subviews];
    for (AddChildView *v in viewsToRemove)
        [v removeFromSuperview];
    for (int i=0; i<array_childList.count; i++)
    {
        AddChildView *childProfile;//=[[ChildProfileImages alloc] init];
        childrenInfo = [array_childList safeObjectAtIndex:i];
        NSArray *nibArray = [[NSBundle mainBundle] loadNibNamed:@"AddChildView" owner:self options:nil];
        childProfile = [nibArray safeObjectAtIndex:0];
                 [childProfile setFrame:CGRectMake(j,0,scrollView_kidsList.frame.size.width,scrollView_kidsList.frame.size.height-15)];
        j=j+scrollView_kidsList.frame.size.width;
        childProfile.lbl_childName.text = childrenInfo.childName;
        childProfile.lbl_ageValue.text = childrenInfo.childAge;
        if ([childrenInfo.childSex isEqualToString:@"M"]) {
            childProfile.lbl_sexValue.text = @"Male";
        }
        else
            childProfile.lbl_sexValue.text = @"Female";
        NSURL *img_url=[NSURL URLWithString:childrenInfo.childThumbImage];
        [childProfile.view_childImage loadImageFromURL:img_url];
        [scrollView_kidsList addSubview:childProfile];
        childProfile = nil;
        totalCount = i;
    }
   scrollView_kidsList.contentSize = CGSizeMake(j,100);
}
#pragma mark - AlertView delegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    if (alertView.tag==100 && buttonIndex==0) {// API call for cancel job
        [self startNetworkActivity:NO];
        NSMutableDictionary *dict_cancleJobDetail = [[NSMutableDictionary alloc]init];
        [dict_cancleJobDetail setSafeObject:self.parentInfo.tokenData forKey:kToken];
        [dict_cancleJobDetail setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
        [dict_cancleJobDetail setSafeObject:self.jobInfo.jobId forKey:kJobId];
        [dict_cancleJobDetail setSafeObject:self.jobInfo.clientUserId forKey:kUserId];
        [self startNetworkActivity:NO];
        SMCoreNetworkManager *networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:kCancleJobApi];
        DDLogInfo(@"%@",kCancleJobApi);
        networkManager.delegate = self;
        [networkManager CancleJob:dict_cancleJobDetail forRequestNumber:1];
        
    }else if (alertView.tag==101 && buttonIndex==1) {//Contact to admin
        NSURL *phoneUrl = [NSURL URLWithString:[NSString stringWithFormat:@"telprompt:%@",self.str_adminContact]];
        if ([[UIApplication sharedApplication] canOpenURL:phoneUrl]) {
            [[UIApplication sharedApplication] openURL:phoneUrl];
        } else {
            UIAlertView * calert = [[UIAlertView alloc]initWithTitle:@"Alert" message:kCallFacilityNotAvailable delegate:nil cancelButtonTitle:@"ok" otherButtonTitles:nil, nil];
            [calert show];
        }
    }
    else if (alertView.tag==1001){
        [[NSNotificationCenter defaultCenter] postNotificationName:@"jobCancled" object:nil];
        [self.navigationController popViewControllerAnimated:YES];
    }
    [self logoutAlert:alertView clickedButtonAtIndex:buttonIndex];
}

#pragma mark - Table view delegates methods

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return array_childList.count;
    
}
// tableview data source metod for set the value of rows cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *CellIdentifier =@"TableViewController";
    
    
    KidsListTableViewCell *cell = (KidsListTableViewCell *)[tbl_kidsList dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        NSArray *nib = [[NSBundle mainBundle] loadNibNamed:@"KidsListTableViewCell" owner:self options:nil];
        cell = [nib objectAtIndex:0];
        
    }
    cell.selectionStyle = UITableViewCellSelectionStyleNone;
    cell.backgroundColor = kBackgroundColor;
    childrenInfo = [array_childList safeObjectAtIndex:indexPath.row];
    cell.lbl_kidsName.text = childrenInfo.childName;
    cell.lbl_age.text = childrenInfo.childAge;
    if ([childrenInfo.childSex isEqualToString:@"M"]) {
        cell.lbl_sex.text = @"Male";
    }
    else
        
        cell.lbl_sex.text = @"Female";
    NSURL *img_url=[NSURL URLWithString:childrenInfo.childThumbImage];
    if (cell.viewKidsImage.imageView.image==nil) {
      
        [cell.viewKidsImage loadImageFromURL:img_url];
//        cell.viewKidsImage.layer.cornerRadius = cell.viewKidsImage.frame.size.height/2;
//        cell.viewKidsImage.clipsToBounds = YES;
    }
    return cell;
}
-(void)tapEmailAndPhoneGesture
{
    UITapGestureRecognizer *tapped1 = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(emailTapped:)];
    UITapGestureRecognizer *tapped2 = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(phoneTapped:)];
    tapped1.numberOfTapsRequired = 1;
    tapped2.numberOfTapsRequired = 1;
    [lbl_sitterEmail setTag:1];
    [lbl_sitterMobileNumber setTag:2];
    [lbl_sitterEmail addGestureRecognizer:tapped1];
    [lbl_sitterMobileNumber addGestureRecognizer:tapped2];
    lbl_sitterMobileNumber.userInteractionEnabled = YES;
    lbl_sitterEmail.userInteractionEnabled = YES;
    
}
-(void)emailTapped:(UIGestureRecognizer*)gesture
{
    DDLogInfo(@">>> %ld", (long)gesture.view.tag);
    if ([MFMailComposeViewController canSendMail]) {
        MFMailComposeViewController * emailController = [[MFMailComposeViewController alloc] init];
        [emailController.navigationBar setTintColor:[UIColor blackColor]];
        emailController.mailComposeDelegate = self;
        [emailController setToRecipients:[NSArray arrayWithObjects:lbl_sitterEmail.text,nil]];
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
-(void)phoneTapped:(UIGestureRecognizer*)gesture
{
    DDLogInfo(@">>> %ld", (long)gesture.view.tag);
    NSURL *phoneUrl = [NSURL URLWithString:[NSString stringWithFormat:@"telprompt:%@",self.jobInfo.sitterPhone]];
    
    if ([[UIApplication sharedApplication] canOpenURL:phoneUrl]) {
        [[UIApplication sharedApplication] openURL:phoneUrl];
    } else {
        UIAlertView * calert = [[UIAlertView alloc]initWithTitle:@"Alert" message:kCallFacilityNotAvailable delegate:nil cancelButtonTitle:@"ok" otherButtonTitles:nil, nil];
        [calert show];
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
                // [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[dict_responseObj valueForKey:kMessage]];
                self.parentInfo.tokenData = [[responseObject objectForKey:kData] objectForKey:kTokenData];
                
                [self showAlertForSelf:self withTitle:nil andMessage:[dict_responseObj valueForKey:kMessage]];
                
            }
            else
            {
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:[dict_responseObj valueForKey:kErrorDisplayMessage] delegate:self cancelButtonTitle:@"Cancel" otherButtonTitles:@"Call", nil];
                [alert setTag:101];
                [alert show];
                self.str_adminContact=[NSString stringWithFormat:@"%@",[[dict_responseObj safeObjectForKey:kErrorMessage] safeObjectForKey:kAdminContact]];
                //[[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            
            break;
            
        case 2:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                self.parentInfo.tokenData = [[responseObject objectForKey:kData] objectForKey:kTokenData];
                [[ApplicationManager getInstance]saveJobDetail:dict_responseObj];
                array_childList = [ApplicationManager getInstance].array_jobChildDetail;
                [self setDataInScrollView];
                [tbl_kidsList reloadData];
                 btn_sitterName.hidden = true;
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
            
            break;
            
            
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
     [self showAlertForSelf:self withTitle:nil andMessage:[error localizedDescription]];
   
    
}
- (void)showAlertForSelf:(id)vc withTitle:(NSString*)title andMessage:(NSString*)message {
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:message delegate:vc cancelButtonTitle:@"OK" otherButtonTitles:nil];
    [alert setTag:1001];
    [alert show];
}
-(NSString*)getEdtDate:(NSDate*)dt{
    NSDate *currentdate = dt;//[NSDate date];
    //Convert into New york time
    NSDateFormatter *dateFormatForSystem=[[NSDateFormatter alloc]init];
    [dateFormatForSystem setTimeZone:[NSTimeZone systemTimeZone]];
    [dateFormatForSystem setDateFormat:@"yyyy-MM-dd HH:mm:ss"];
    NSString *str_currentDate = [dateFormatForSystem stringFromDate:currentdate];
  //  NSLog(@"system time  --   %@",str_currentDate);
    NSDateFormatter *dateFormatForUTC = [[NSDateFormatter alloc] init];
    dateFormatForUTC.dateFormat = @"yyyy-MM-dd HH:mm:ss";
    
    NSTimeZone *gmt = [NSTimeZone timeZoneWithAbbreviation:@"UTC"];
    [dateFormatForUTC setTimeZone:gmt];
    NSString *utcTime = [dateFormatForUTC stringFromDate:[dateFormatForSystem dateFromString:str_currentDate]];
   // NSLog(@"UTC -- %@",utcTime);
    NSDateFormatter *dateFormatForEDT = [[NSDateFormatter alloc] init];
    dateFormatForEDT.dateFormat = @"yyyy-MM-dd HH:mm:ss";
    NSTimeZone *edt = [NSTimeZone timeZoneWithAbbreviation:@"EDT"];
    [dateFormatForEDT setTimeZone:edt];
    NSString *edtTime = [dateFormatForEDT stringFromDate:[dateFormatForUTC dateFromString:utcTime]];
    return edtTime;

}
@end
