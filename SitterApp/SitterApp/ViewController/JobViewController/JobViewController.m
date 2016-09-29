//
//  JobViewController.m
//  SitterApp
//
//  Created by Shilpa Gade on 03/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "JobViewController.h"
#import "JobDetailViewController.h"
#import "JobHistoryCell.h"

@interface JobViewController ()

@end

@implementation JobViewController
@synthesize sitterInfo;
- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.sitterInfo=[ApplicationManager getInstance].sitterInfo;
    self.jobList=[ApplicationManager getInstance].jobList;
    self.array_JobList=[[NSMutableArray alloc] init];
    dict_JobRequest=[[NSMutableDictionary alloc] init];
    [dict_JobRequest setSafeObject:self.sitterInfo.sitterId forKey:kUserId];
    [dict_JobRequest setSafeObject:self.sitterInfo.str_TokenData forKey:kToken];
    [dict_JobRequest setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    self.totalPages=0;
    self.currentPage=1;
    self.isloading=NO;
    [tbl_JobList reloadData];
    
    [self.view updateConstraints];
    if (dict_JobRequest !=nil){
        [self performSelectorOnMainThread:@selector(startNetworkActivity:) withObject:[NSNumber numberWithInteger:1] waitUntilDone:NO];
        [self jobRequest:dict_JobRequest];
    }
   
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(updateChildCount) name:kUpdateChildCount object:nil];
    self.view.backgroundColor=kBackgroundColor;
}


- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    self.view.backgroundColor=kBackgroundColor;
    tbl_JobList.backgroundColor=kBackgroundColor;
    self.view_totalEarned.backgroundColor=kBackgroundColor;
    lbl_totalEarned.textColor=kColorGrayLight;
    lbl_totalOwed.textColor=kColorGrayLight;
    
    switch (self.flag) {
        case 1:
            self.navigationItem.title=@"Open Jobs";
            break;
        case 2:
            self.navigationItem.title=@"Scheduled Jobs";
            break;
        case 3:
            self.navigationItem.title=@"Active Jobs";
            break;
        case 4:
            self.navigationItem.title=@"Completed Jobs";
            break;
        case 5:
            self.navigationItem.title=@"Closed Jobs";
            break;
        case 6:
            self.navigationItem.title=@"Cancelled Jobs";
            break;
            
        default:
            break;
    }
    
}
-(void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    if (self.flag==4)// Job history complete job
    {
        [self.view_totalEarned setHidden:NO];
    }else{
        [self.view_totalEarned setHidden:YES];
    }
    
}

-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    if (self.flag==4)// Job history complete job
    {
        [self.view_totalEarned setHidden:NO];
    }else{
        [self.view_totalEarned setHidden:YES];
        [tbl_JobList setFrame:CGRectMake(tbl_JobList.frame.origin.x, self.view_totalEarned.frame.origin.y, tbl_JobList.frame.size.width, tbl_JobList.frame.size.height+self.view_totalEarned.frame.size.height)];
    }
}



-(void)updateChildCount{

    if (dict_JobRequest !=nil){
        self.totalPages=0;
        self.currentPage=1;
        self.isloading=NO;
        [self.array_JobList removeAllObjects];
        [self performSelectorOnMainThread:@selector(startNetworkActivity:) withObject:[NSNumber numberWithInteger:1] waitUntilDone:NO];
        [self jobRequest:dict_JobRequest];
    }
}
#pragma mark-UITableViewDataSource
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    if(self.currentPage<self.totalPages)
        return self.array_JobList.count+1;
    return  self.array_JobList.count;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *CellIdentifier = @"JobListTableViewCell";
    static NSString *JobHistoryCellIdentifier = @"JobHistoryCell";
    JobListTableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    JobHistoryCell *jobHistoryCell = [tableView dequeueReusableCellWithIdentifier:JobHistoryCellIdentifier];
    
    if(indexPath.row<self.array_JobList.count)
    {
        switch (self.flag) {
            case 4:
            {
                if (jobHistoryCell == nil)
                {
                    NSArray *nibArray=[[NSBundle mainBundle] loadNibNamed:@"JobHistoryCell" owner:self options:nil];
                    jobHistoryCell = [nibArray safeObjectAtIndex:0];
                    jobHistoryCell.accessoryType=UITableViewCellAccessoryNone;
                    jobHistoryCell.backgroundColor=[UIColor clearColor];
                    jobHistoryCell.viewCellBg.layer.borderWidth=1.0;
                    jobHistoryCell.viewCellBg.layer.borderColor=[UIColor lightGrayColor].CGColor;
                    
                }
                NSDictionary *dict_jobHistory=[self.array_JobList safeObjectAtIndex:indexPath.row];
                //DDLogInfo(@"dict_job=%@",dict_jobHistory);
                jobHistoryCell.lbl_jobNumber.text=[dict_jobHistory safeObjectForKey:kJobId];
                jobHistoryCell.lbl_jobAddress.text=[[dict_jobHistory safeObjectForKey:kAddress] safeObjectForKey:kStreetAddress];
                jobHistoryCell.lbl_startDate.text=[dict_jobHistory safeObjectForKey:kJobStartDate];
                jobHistoryCell.lbl_endDate.text=[dict_jobHistory safeObjectForKey:kJobEndDate];
                jobHistoryCell.lbl_ActualTime.text=[dict_jobHistory safeObjectForKey:kActual_end_date];
                jobHistoryCell.lbl_rate.text=[NSString stringWithFormat:@"$%@/%@/%@",[dict_jobHistory safeObjectForKey:kRate],[dict_jobHistory safeObjectForKey:kActual_child_count],[dict_jobHistory safeObjectForKey:kTotalHours]];
               jobHistoryCell.lbl_jobEarned.text=[NSString stringWithFormat:@"$%@",[dict_jobHistory safeObjectForKey:kTotal_paid]];
                if ([[[dict_jobHistory safeObjectForKey:kPayment_Status] lowercaseString] isEqualToString:@"paid"]) {
                    [jobHistoryCell.sw_paidStatus setOn:YES];
                    [jobHistoryCell.img_paidStatus setImage:[UIImage imageNamed:@"right"]];
                    [jobHistoryCell.img_paidStatus setContentMode:UIViewContentModeScaleAspectFit];
                }else{
                    [jobHistoryCell.sw_paidStatus setOn:NO];
                    [jobHistoryCell.img_paidStatus setImage:[UIImage imageNamed:@"cross"]];
                    [jobHistoryCell.img_paidStatus setContentMode:UIViewContentModeScaleAspectFit];
                }
                return jobHistoryCell;
                break;
            }
            default:
            {
                if (cell == nil)
                {
                    NSArray *nibArray=[[NSBundle mainBundle] loadNibNamed:@"JobListTableViewCell" owner:self options:nil];
                    cell = [nibArray safeObjectAtIndex:0];
                    cell.accessoryType=UITableViewCellAccessoryNone;
                    cell.backgroundColor=kBackgroundColor;
                    cell.viewCellBg.layer.borderWidth=1.0;
                    cell.viewCellBg.layer.borderColor=[UIColor lightGrayColor].CGColor;
                    
                }
                
                NSDictionary *dict_job=[self.array_JobList safeObjectAtIndex:indexPath.row];
                DDLogInfo(@"dict_job=%@",dict_job);
                cell.lbl_ShowJobNumber.text=[dict_job safeObjectForKey:kJobId];
                cell.lbl_JobShowStartDate.text=[dict_job safeObjectForKey:kJobStartDate];
                [cell.lbl_JobShowStartDate sizeToFit];
                cell.lbl_JobShowEndDate.text=[dict_job safeObjectForKey:kJobEndDate];
                [cell.lbl_JobShowEndDate sizeToFit];
                cell.lbl_ShowChildrenCount.text=[dict_job safeObjectForKey:kActual_child_count];
                cell.lbl_showArea.text=[[dict_job safeObjectForKey:kAddress] safeObjectForKey:kCity];
                NSArray *array_Children=[dict_job safeObjectForKey:@"children"];
                NSString *strChildAgeInYear=@"";
                NSString *strChildAgeInMonth=@"";
                NSString *strChildAgeInDays=@"";
                
                
                for (NSDictionary *d in array_Children) {
                    NSString *strAge=[d safeObjectForKey:@"age"];
                    if ([strAge rangeOfString:@"Years"].length>0) {
                        strAge=[strAge stringByReplacingOccurrencesOfString:@"Years" withString:@""];
                        strAge=trimedString(strAge);
                        strChildAgeInYear=[strChildAgeInYear stringByAppendingString:[NSString stringWithFormat:@" %@,",strAge]];
                    }else if ([strAge rangeOfString:@"Months"].length>0) {
                        strAge=[strAge stringByReplacingOccurrencesOfString:@"Months" withString:@""];
                        strAge=trimedString(strAge);
                        strChildAgeInMonth=[strChildAgeInMonth stringByAppendingString:[NSString stringWithFormat:@" %@,",strAge]];
                    }else if ([strAge rangeOfString:@"Days"].length>0) {
                        strAge=[strAge stringByReplacingOccurrencesOfString:@"Days" withString:@""];
                        strAge=trimedString(strAge);
                        strChildAgeInDays=[strChildAgeInDays stringByAppendingString:[NSString stringWithFormat:@" %@,",strAge]];
                    }
                }
                if ([strChildAgeInYear length]>0) {
                    strChildAgeInYear=[strChildAgeInYear substringToIndex:[strChildAgeInYear length]-1];
                    strChildAgeInYear=[strChildAgeInYear stringByAppendingString:[NSString stringWithFormat:@"%@",@" Year"]];
                }
                if ([strChildAgeInMonth length]>0) {
                    strChildAgeInMonth=[strChildAgeInMonth substringToIndex:[strChildAgeInMonth length]-1];
                    if ([strChildAgeInYear length]>0) {
                        strChildAgeInYear=[strChildAgeInYear stringByAppendingString:[NSString stringWithFormat:@","]];
                    }
                        strChildAgeInMonth=[strChildAgeInMonth stringByAppendingString:[NSString stringWithFormat:@"%@",@" Months"]];
                }
                if ([strChildAgeInDays length]>0) {
                    strChildAgeInDays=[strChildAgeInDays substringToIndex:[strChildAgeInDays length]-1];
                    if ([strChildAgeInYear length]>0 && [strChildAgeInMonth length] == 0) {
                        strChildAgeInYear=[strChildAgeInYear stringByAppendingString:[NSString stringWithFormat:@","]];
                    }
                    if ([strChildAgeInMonth length]>0) {
                        strChildAgeInMonth=[strChildAgeInMonth stringByAppendingString:[NSString stringWithFormat:@","]];
                    }
                        strChildAgeInDays=[strChildAgeInDays stringByAppendingString:[NSString stringWithFormat:@"%@",@" Days"]];
                }
                
                
                cell.lbl_ShowChildAge.text=[NSString stringWithFormat:@"%@ %@ %@",trimedString(strChildAgeInYear),trimedString(strChildAgeInMonth),trimedString(strChildAgeInDays)];
                
                return cell;
                break;
            }
        }
        
    }
    else
    {
        UIActivityIndicatorView *actView;
        static NSString *LoadingCellIdentifier = @"loadingCell";
        UITableViewCell *loadingCell=[tbl_JobList dequeueReusableCellWithIdentifier:LoadingCellIdentifier];
        if (loadingCell==nil) {
            loadingCell =[[UITableViewCell alloc]initWithStyle:UITableViewCellStyleDefault reuseIdentifier:LoadingCellIdentifier];
            loadingCell.backgroundColor=kBackgroundColor;
            loadingCell.selectionStyle = UITableViewCellSelectionStyleNone;
            
        }
        
        actView = [[UIActivityIndicatorView alloc]initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleGray];
        actView.color = [UIColor blackColor];
        [actView startAnimating];
        actView.center = CGPointMake((tbl_JobList.frame.size.width)/2, 20);
        [loadingCell.contentView addSubview:actView];
        if (self.totalPages>self.currentPage && !self.isloading)
        {
            self.currentPage++;
            [self jobRequest:dict_JobRequest];
            self.isloading=YES;
        }
        [actView startAnimating];
        actView.hidden=NO;
        loadingCell.hidden=NO;
        return loadingCell;
    }
    return nil;
}


#pragma mark-UITableViewDelegate
- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section{
    return 0.01;
}

- (CGFloat)tableView:(UITableView *)tableView heightForFooterInSection:(NSInteger)section{
    return 0.01;
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (indexPath.row==self.array_JobList.count) {// for loading cell
        return 40;
    }
    if (self.flag==4) {
        return 165;
    }
    return 146;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
    if (self.flag!=4) {
        
        if (self.flag==2) {
            NSDictionary *dict_jobData =[self.jobList.array_ScheduledJob safeObjectAtIndex:indexPath.row];
            if ([[[dict_jobData safeObjectForKey:kJobStatus] lowercaseString] isEqualToString:@"inactive"]) {
                
                ActiveJobDetailViewController *viewActiveJob=[[ActiveJobDetailViewController alloc]initWithNibName:@"ActiveJobDetailViewController_inactive" bundle:nil];
                viewActiveJob.jobTypeFlag=self.flag;
                viewActiveJob.indexPath=(int)indexPath.row;
                self.navigationItem.title=@"";
                [self.navigationController pushViewController:viewActiveJob animated:YES];
            }else{
                ActiveJobDetailViewController *viewActiveJob=[[ActiveJobDetailViewController alloc]initWithNibName:@"ActiveJobDetailViewController" bundle:nil];
                viewActiveJob.jobTypeFlag=self.flag;
                viewActiveJob.indexPath=(int)indexPath.row;
                self.navigationItem.title=@"";
                [self.navigationController pushViewController:viewActiveJob animated:YES];
            }

        }else if (self.flag==3) {
            NSDictionary *dict_jobData =[self.jobList.array_ActiveJob safeObjectAtIndex:indexPath.row];
            if ([[[dict_jobData safeObjectForKey:kJobStatus] lowercaseString] isEqualToString:@"inactive"]) {
                ActiveJobDetailViewController *viewActiveJob=[[ActiveJobDetailViewController alloc]initWithNibName:@"ActiveJobDetailViewController_inactive" bundle:nil];
                viewActiveJob.jobTypeFlag=self.flag;
                viewActiveJob.indexPath=(int)indexPath.row;
                self.navigationItem.title=@"";
                [self.navigationController pushViewController:viewActiveJob animated:YES];
            }else{
                ActiveJobDetailViewController *viewActiveJob=[[ActiveJobDetailViewController alloc]initWithNibName:@"ActiveJobDetailViewController" bundle:nil];
                viewActiveJob.jobTypeFlag=self.flag;
                viewActiveJob.indexPath=(int)indexPath.row;
                self.navigationItem.title=@"";
                [self.navigationController pushViewController:viewActiveJob animated:YES];
            }
            
        }
        else{
            JobDetailViewController *jobDetailViewController=[[JobDetailViewController alloc] initWithNibName:@"JobDetailViewController" bundle:nil];
            jobDetailViewController.indexPath=(int)indexPath.row;//index path for identify the array index which will be show on next screen
            jobDetailViewController.flag=self.flag;//flag for identify the which type of job is selected
            self.navigationItem.title=@"";
            [self.navigationController pushViewController:jobDetailViewController animated:YES];
        }
    }
    
}



#pragma mark-APICalling
/**
 This method is used for call three apis(open jobs, schedule jobs,completed jobs)
 @param NSMutableDictionary dict_jobRequest
 @return void
 */

-(void)jobRequest:(NSMutableDictionary *)dict_jobRequest
{
    
    SMCoreNetworkManager *networkManager;
    NSString *string_Url=[NSString stringWithFormat:@"%@/%d",kJobList_API,self.currentPage];
    networkManager= [[SMCoreNetworkManager alloc] initWithBaseURLString:string_Url];
    networkManager.delegate = self;
    if (self.flag)
    {
        switch (self.flag)
        {
            case 1:
                [dict_jobRequest setSafeObject:kPending forKey:kJobStatus];
                [networkManager getJobList:dict_jobRequest forRequestNumber:1];
                break;
            case 2:
                [dict_jobRequest setSafeObject:kConfirmed forKey:kJobStatus];
                [networkManager getJobList:dict_jobRequest forRequestNumber:2];
                
                break;
            case 3:
                [dict_jobRequest setSafeObject:kActiveJob forKey:kJobStatus];
                [networkManager getJobList:dict_jobRequest forRequestNumber:3];
                break;
            case 4:
                [dict_jobRequest setSafeObject:kCompleted forKey:kJobStatus];
                [networkManager getJobList:dict_jobRequest forRequestNumber:4];
                break;
            case 5:
                [dict_jobRequest setSafeObject:kClosedJob forKey:kJobStatus];
                [networkManager getJobList:dict_jobRequest forRequestNumber:5];
                break;
            case 6:
                [dict_jobRequest setSafeObject:kCancelledJob forKey:kJobStatus];
                [networkManager getJobList:dict_jobRequest forRequestNumber:6];
                break;
            default:
                break;
        }
    }
}
#pragma mark Separator Methods

- (void)addSeparatorImageToCell:(UITableViewCell *)cell
{
    UIImageView *separatorImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, cell.contentView.frame.size.height - 1, tbl_JobList.frame.size.width, 1)];
    [separatorImageView setImage:[ApplicationManager imageWithColor:kColorAppGreen]];
    separatorImageView.opaque = YES;
    [cell.contentView addSubview:separatorImageView];
}
#pragma mark-OtherMethods
-(NSString *)changeFormatOfDate:(NSString *)str_Date
{
    NSDateFormatter *dateFormatter=[[NSDateFormatter alloc] init];
    [dateFormatter setDateFormat:@"mm/dd/yy hh:mm:s tt"];
    NSDate *dateFromString = [dateFormatter dateFromString:str_Date];
    return  [dateFormatter stringFromDate:dateFromString];
}

//-------------------------------------------------------------------------------
//method is used for changing the format of date:
//-------------------------------------------------------------------------------
-(NSString *)changeFormatOfDate:(NSString *)str_date andDateFormate:(NSString *)dateFormate
{
    NSString *dateStr =str_date;
    NSDateFormatter *dateFormatter1 = [[NSDateFormatter alloc] init];
    [dateFormatter1 setDateFormat:dateFormate];
    NSDate *date = [dateFormatter1 dateFromString:dateStr];
    // return destinationDate;
    NSDateFormatter *dateFormatters = [[NSDateFormatter alloc] init];
    [dateFormatters setDateFormat:@"mm/dd/yy hh a"];
    [dateFormatters setTimeZone:[NSTimeZone localTimeZone]];
    dateStr = [dateFormatters stringFromDate: date];
    return dateStr;
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
                [[ApplicationManager getInstance] saveJobList:dict_responseObj andJobType:kOpenJob];//for global use
                [self.array_JobList addObjectsFromArray:[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:kJobList]];
                
                
                NSDictionary *dict_Pagination=[[dict_responseObj objectForKey:kData] objectForKey:kPagination];
                self.totalPages=[[dict_Pagination objectForKey:kPageCount] intValue];
                self.isloading=NO;
                [UIApplication sharedApplication].applicationIconBadgeNumber = [[[dict_responseObj safeObjectForKey:kData ] safeObjectForKey:@"notification_count"] integerValue];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                self.isloading=NO;
                
            }
            break;
            
        case 2:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [[ApplicationManager getInstance] saveJobList:dict_responseObj andJobType:kScheduledJob];//for global use
                [self.array_JobList addObjectsFromArray:[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:kJobList]];
                NSDictionary *dict_Pagination=[[dict_responseObj objectForKey:kData] objectForKey:kPagination];
                self.totalPages=[[dict_Pagination objectForKey:kPageCount] intValue];
                self.isloading=NO;
                [UIApplication sharedApplication].applicationIconBadgeNumber = [[[dict_responseObj safeObjectForKey:kData ] safeObjectForKey:@"notification_count"] integerValue];
                
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                self.isloading=NO;
                
            }
            break;
        case 3:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [[ApplicationManager getInstance] saveJobList:dict_responseObj andJobType:kActiveJob];//for global use
                [self.array_JobList addObjectsFromArray:[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:kJobList]];
                NSDictionary *dict_Pagination=[[dict_responseObj objectForKey:kData] objectForKey:kPagination];
                self.totalPages=[[dict_Pagination objectForKey:kPageCount] intValue];
                self.isloading=NO;
                [UIApplication sharedApplication].applicationIconBadgeNumber = [[[dict_responseObj safeObjectForKey:kData ] safeObjectForKey:@"notification_count"] integerValue];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                self.isloading=NO;
                
            }
            break;
            
        case 4:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [[ApplicationManager getInstance] saveJobList:dict_responseObj andJobType:kCompletedJob];//for global use
                [self.array_JobList addObjectsFromArray:[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:kJobList]];
                NSDictionary *dict_Pagination=[[dict_responseObj objectForKey:kData] objectForKey:kPagination];
                self.totalPages=[[dict_Pagination safeObjectForKey:kPageCount] intValue];
                lbl_totalEarned.text=[NSString stringWithFormat:@"$%@",[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:kTotalEarned]];
                lbl_totalOwed.text=[NSString stringWithFormat:@"$%@",[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:kTotalOwned]];
                
                self.isloading=NO;
                [UIApplication sharedApplication].applicationIconBadgeNumber = [[[dict_responseObj safeObjectForKey:kData ] safeObjectForKey:@"notification_count"] integerValue];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                self.isloading=NO;
                
            }
            break;
        case 5:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [[ApplicationManager getInstance] saveJobList:dict_responseObj andJobType:kClosedJob];//for global use
                [self.array_JobList addObjectsFromArray:[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:kJobList]];
                NSDictionary *dict_Pagination=[[dict_responseObj objectForKey:kData] objectForKey:kPagination];
                self.totalPages=[[dict_Pagination objectForKey:kPageCount] intValue];
                self.isloading=NO;
                [UIApplication sharedApplication].applicationIconBadgeNumber = [[[dict_responseObj safeObjectForKey:kData ] safeObjectForKey:@"notification_count"] integerValue];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                self.isloading=NO;
                
            }
            break;
        case 6:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [[ApplicationManager getInstance] saveJobList:dict_responseObj andJobType:kCancelledJob];//for global use
                [self.array_JobList addObjectsFromArray:[[dict_responseObj safeObjectForKey:kData] safeObjectForKey:kJobList]];
                NSDictionary *dict_Pagination=[[dict_responseObj objectForKey:kData] objectForKey:kPagination];
                self.totalPages=[[dict_Pagination objectForKey:kPageCount] intValue];
                self.isloading=NO;
                [UIApplication sharedApplication].applicationIconBadgeNumber = [[[dict_responseObj safeObjectForKey:kData ] safeObjectForKey:@"notification_count"] integerValue];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                self.isloading=NO;
                
            }
            break;
            
        default:
            break;
    }
    [tbl_JobList reloadData];
    
}

- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    self.isloading=NO;
    [self stopNetworkActivity];
    // NSError *errorcode=(NSError *)error;
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
    
}

@end
