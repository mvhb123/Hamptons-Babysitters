//
//  JobListViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 16/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "JobListViewController.h"
#import "JobListTableViewCell.h"
#import "CompletedJobListTableViewCell.h"
#import "JobDetailViewController.h"
#import "LoadingCell.h"
@interface JobListViewController ()

@end

@implementation JobListViewController
@synthesize dict_dobDetail;

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    currentPage=1;//For pagination
    totalPages=0;
    self.view.backgroundColor=kViewBackGroundColor;
    array_jobsDetail = [[NSMutableArray alloc]init];
    if (self.jobType==1) {
        dict_dobDetail = [[NSMutableDictionary alloc]init];
        [dict_dobDetail setSafeObject:@"pending" forKey:kJobStatus];
    }
    array_jobList = [[NSMutableArray alloc]init];
    [dict_dobDetail setSafeObject:self.parentInfo.tokenData forKey:kToken];
    [dict_dobDetail setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [dict_dobDetail setSafeObject:self.parentInfo.parentUserId forKey:kUserId];
    if ([[dict_dobDetail  safeObjectForKey:kJobStatus]isEqualToString:@"confirmed"]) {
        self.navigationItem.title = @"Scheduled Jobs";
    }
    else if ([[dict_dobDetail  safeObjectForKey:kJobStatus]isEqualToString:@"pending"])
    {
        self.navigationItem.title = @"Open Jobs";
    } else if ([[dict_dobDetail  safeObjectForKey:kJobStatus]isEqualToString:@"active"])
    {
        self.navigationItem.title = @"Active Jobs";
    }
    else if ([[dict_dobDetail  safeObjectForKey:kJobStatus]isEqualToString:@"completed"]){
        self.navigationItem.title = @"Completed Jobs";
    }else{
        self.navigationItem.title = @"Cancelled Jobs";
    }
    [[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(receiveEvent1:) name:@"jobCancled" object:nil];
   [self callAPI];
}
-(void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:YES];
    //currentPage=1;//For pagination
    //totalPages=0;

    
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
- (void)receiveEvent1:(NSNotification *)notification {
    
    currentPage=1;//For pagination
    totalPages=0;
    [array_jobList removeAllObjects];
    [self callAPI];
}
-(void)callAPI
{
    [array_jobsDetail removeAllObjects];
    self.parentInfo = [ApplicationManager getInstance].parentInfo;
    if (currentPage == 1) {
       [self startNetworkActivity:YES];
      
    }
    NSString *string_Url=[NSString stringWithFormat:@"%@/%d",kJobsTypeRequestApi,currentPage];
    networkManager = [[SMCoreNetworkManager alloc] initWithBaseURLString:string_Url];
    DDLogInfo(@"%@",string_Url);
    networkManager.delegate = self;
    [networkManager JobsType:self.dict_dobDetail forRequestNumber:1];
  
}
#pragma mark UITableViewDatasource
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
   if(currentPage<totalPages){
        return array_jobList.count+1;
    }
    return array_jobList.count;
}
// tableview data source metod for set the value of rows cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *CellIdentifier =@"JobListTableViewCell";
    static NSString *CompleteJobCellIdentifier =@"CompletedJobListTableViewCell";
    JobListTableViewCell *cell = (JobListTableViewCell *)[tbl_jobList dequeueReusableCellWithIdentifier:CellIdentifier];
    CompletedJobListTableViewCell *CompleteJobcell = (CompletedJobListTableViewCell *)[tbl_jobList dequeueReusableCellWithIdentifier:CompleteJobCellIdentifier];
    if(indexPath.row<array_jobList.count){// For Check data cell
        if ([[dict_dobDetail  safeObjectForKey:kJobStatus]isEqualToString:@"completed"]) {
            if (CompleteJobcell == nil) {
                NSArray *nib = [[NSBundle mainBundle] loadNibNamed:@"CompletedJobListTableViewCell" owner:self options:nil];
                CompleteJobcell = [nib objectAtIndex:0];
                CompleteJobcell.viewCellBG.layer.borderWidth=1.0;
                CompleteJobcell.viewCellBG.layer.borderColor=[UIColor lightGrayColor].CGColor;
            }
            CompleteJobcell.selectionStyle = UITableViewCellSelectionStyleNone;
            CompleteJobcell.contentView.backgroundColor =  kViewBackGroundColor;
            jobInfo = [array_jobList safeObjectAtIndex:indexPath.row];
            CompleteJobcell.lbl_jobNumber.text = jobInfo.jobId;
            CompleteJobcell.lbl_startDate.text = [self convertDateFormate:jobInfo.jobStartDate andDateFormate:@"MMM dd, yyyy hh:mm a"];
            CompleteJobcell.lbl_endDate.text = [self convertDateFormate:jobInfo.jobEndDate andDateFormate:@"MMM dd, yyyy hh:mm a"];
            CompleteJobcell.lbl_status.text = jobInfo.jobStatus;
            CompleteJobcell.lbl_rateValue.text =[NSString stringWithFormat:@"$%@/%@",jobInfo.rate,jobInfo.totalHours];
            CompleteJobcell.lbl_totalChargeValue.text =[NSString stringWithFormat:@"$%@",jobInfo.totalPaid];
            if (![jobInfo.sitterUserId isEqualToString:@""]) {
                
                NSMutableAttributedString *string = [[NSMutableAttributedString alloc] initWithString:[NSString stringWithFormat:@"Babysitter "]];
                NSMutableAttributedString *stringName = [[NSMutableAttributedString alloc] initWithString:[NSString stringWithFormat:@"%@ %@",jobInfo.sitterFirstName,jobInfo.sitterLastName]];
                
                [string setAttributes:@{NSFontAttributeName:[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize]}
                                range:(NSRange){0,string.length}];
                [stringName setAttributes:@{NSFontAttributeName:[UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize]}
                                    range:(NSRange){0,stringName.length}];
                [string appendAttributedString:stringName];
                CompleteJobcell.lbl_babySitterName.attributedText = string;
            }
            else
                CompleteJobcell.lbl_babySitterName.text = @"";
            return CompleteJobcell;
        }else{
       if (cell == nil) {
        NSArray *nib = [[NSBundle mainBundle] loadNibNamed:@"JobListTableViewCell" owner:self options:nil];
        cell = [nib objectAtIndex:0];
           cell.viewCellBG.layer.borderWidth=1.0;
           cell.viewCellBG.layer.borderColor=[UIColor lightGrayColor].CGColor;
       }
       cell.selectionStyle = UITableViewCellSelectionStyleNone;
      cell.contentView.backgroundColor =  kViewBackGroundColor;
       jobInfo = [array_jobList safeObjectAtIndex:indexPath.row];
       cell.lbl_jobNumber.text = jobInfo.jobId;
        cell.lbl_startDate.text = [self convertDateFormate:jobInfo.jobStartDate andDateFormate:@"MMM dd, yyyy hh:mm a"];
      cell.lbl_endDate.text = [self convertDateFormate:jobInfo.jobEndDate andDateFormate:@"MMM dd, yyyy hh:mm a"];
       cell.lbl_status.text = jobInfo.jobStatus;
       if (![jobInfo.sitterUserId isEqualToString:@""]) {
       
           NSMutableAttributedString *string = [[NSMutableAttributedString alloc] initWithString:[NSString stringWithFormat:@"Babysitter "]];
           NSMutableAttributedString *stringName = [[NSMutableAttributedString alloc] initWithString:[NSString stringWithFormat:@"%@ %@",jobInfo.sitterFirstName,jobInfo.sitterLastName]];
     
           [string setAttributes:@{NSFontAttributeName:[UIFont fontWithName:RobotoMediumFont size:LabelFieldFontSize]}
                              range:(NSRange){0,string.length}];
           [stringName setAttributes:@{NSFontAttributeName:[UIFont fontWithName:RobotoRegularFont size:LabelFieldFontSize]}
                           range:(NSRange){0,stringName.length}];
           [string appendAttributedString:stringName];
          cell.lbl_babySitterName.attributedText = string;
       }
       else
          cell.lbl_babySitterName.text = @"";
        return cell;
        }
    }
    else{//For Lading cell
        UIActivityIndicatorView *actView;
        static NSString *LoadingCellIdentifier = @"loadingCell";
        UITableViewCell *loadingCell=[tbl_jobList dequeueReusableCellWithIdentifier:LoadingCellIdentifier];
        if (loadingCell == nil) {
            loadingCell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:LoadingCellIdentifier];
        }
        loadingCell.backgroundColor=kViewBackGroundColor;
        loadingCell.selectionStyle = UITableViewCellSelectionStyleNone;
        actView = [[UIActivityIndicatorView alloc]initWithActivityIndicatorStyle:UIActivityIndicatorViewStyleGray];
        actView.color = [UIColor blackColor];
        [actView startAnimating];
        actView.center = CGPointMake((tbl_jobList.frame.size.width)/2, 40);
        [loadingCell.contentView addSubview:actView];
        if (totalPages>currentPage)
        {
            currentPage++;
            [self callAPI];
            
        }
        [actView startAnimating];
        actView.hidden=NO;
        loadingCell.hidden=NO;
        return loadingCell;
    }
    return nil;
}
-(void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
    NSMutableDictionary *dict_jobDetailData = [[NSMutableDictionary alloc]init];
    jobInfo = [array_jobList safeObjectAtIndex:indexPath.row];
    [dict_jobDetailData setSafeObject:jobInfo.jobId forKey:kJobId];
   JobDetailViewController *jobDetail = [[JobDetailViewController alloc]initWithNibName:@"JobDetailViewController" bundle:nil];
    jobDetail.dict_jobsDetail = [dict_jobDetailData mutableCopy];
    if ([[dict_dobDetail  safeObjectForKey:kJobStatus]isEqualToString:@"active"]){
        jobDetail.checkJobType=1;
    }
    [self.navigationController pushViewController:jobDetail animated:YES];
 
}
- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath;
{
    if ([[dict_dobDetail  safeObjectForKey:kJobStatus]isEqualToString:@"completed"]){
        return 154;
    }
    return 120;
}
- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section{
    return 0.01;
}

- (CGFloat)tableView:(UITableView *)tableView heightForFooterInSection:(NSInteger)section{
    return 0.05;
}
#pragma mark - SMCoreNetworkManagerDelegate

- (void)requestDidSucceedWithResponseObject:(id)responseObject
                                   withTask:(NSURLSessionDataTask *)task
                              withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    DDLogInfo(@"%@",responseObject);
    switch (requestId) {
        case 1: // for load job list
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
                [UIApplication sharedApplication].applicationIconBadgeNumber = [[[dict_responseObj safeObjectForKey:kData ] safeObjectForKey:@"notification_count"] integerValue];
                [ApplicationManager getInstance].parentInfo.tokenData =[[responseObject objectForKey:kData] objectForKey:kTokenData];
                array_jobsDetail = [[[dict_responseObj safeObjectForKey:kData]safeObjectForKey:kJobList] mutableCopy];
                [[ApplicationManager getInstance]saveJobList:array_jobsDetail];
                [array_jobList addObjectsFromArray:[ApplicationManager getInstance].array_jobList];
                NSDictionary *dict_Pagination=[[dict_responseObj objectForKey:kData] objectForKey:kPagination];
                totalPages=[[dict_Pagination objectForKey:kPageCount] intValue];
                [tbl_jobList reloadData];
                
            }
            else
            {
               if (array_jobList.count == 0) {
                    [tbl_jobList reloadData];
                    [self showAlertForSelf:self withTitle:nil andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
                }
            }
            
            break;
        case 6:// for logout
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
#pragma mark Separator Methods

- (void)addSeparatorImageToCell:(UITableViewCell *)cell
{
    UIImageView *separatorImageView = [[UIImageView alloc] initWithFrame:CGRectMake(0, cell.contentView.frame.size.height - 1, tbl_jobList.frame.size.width, 1)];
    [separatorImageView setImage:[ApplicationManager imageWithColor:kColorAppGreen]];
    separatorImageView.opaque = YES;
    [cell.contentView addSubview:separatorImageView];
}
- (void)showAlertForSelf:(id)vc withTitle:(NSString*)title andMessage:(NSString*)message {
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:message delegate:vc cancelButtonTitle:@"OK" otherButtonTitles:nil];
    [alert setTag:1001];
    [alert show];
}
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
    if (alertView.tag == 1001) {
        [self.navigationController popViewControllerAnimated:YES];
    }
    [self logoutAlert:alertView clickedButtonAtIndex:buttonIndex];
}
@end
