//
//  JobDetailViewController.m
//  SitterApp
//
//  Created by Shilpa Gade on 09/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "JobDetailViewController.h"

@interface JobDetailViewController ()

@end

@implementation JobDetailViewController
@synthesize indexPath,flag,sitterInfo;

- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    self.title=@"Job Detail";
    self.array_Jobs=[[NSMutableArray alloc] init];
    self.jobList=[ApplicationManager getInstance].jobList;
    self.sitterInfo=[ApplicationManager getInstance].sitterInfo;
    
    switch (self.flag) // flag = 1 for open job, 2= schedule job , 3= completed job
    {
        case 1:
        {
            dict_jobDetail=[self.jobList.array_OpenJob safeObjectAtIndex:indexPath];
            break;
        }
        case 2:
        {
            dict_jobDetail=[self.jobList.array_ScheduledJob safeObjectAtIndex:indexPath];
            break;
        }
        case 3:
        {
            dict_jobDetail=[self.jobList.array_ActiveJob safeObjectAtIndex:indexPath];
            break;
        }
        case 4:
        {
            dict_jobDetail=[self.jobList.array_CompletedJob safeObjectAtIndex:indexPath];
            break;
        }
        case 5:
        {
            dict_jobDetail=[self.jobList.array_ClosedJob safeObjectAtIndex:indexPath];
            break;
        }
        case 6:
        {
            dict_jobDetail=[self.jobList.array_CancelledJob safeObjectAtIndex:indexPath];
            break;
        }
        default:
            break;
    }
    pageNo=0;
    [btn_next setHidden:YES];
    [btn_previous setHidden:YES];
    DDLogInfo(@"data dict %@",dict_jobDetail);
   // [self.backgroundScrollView setContentSize:CGSizeMake(self.view.bounds.size.width,MAX(725, self.view.bounds.size.height))];
    self.view.backgroundColor=kBackgroundColor;
    view_bottom.backgroundColor=kBackgroundColor;
    [self.backgroundScrollView setHidden:YES];
    [self setFontAndColrForView];
}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}
-(void)viewWillAppear:(BOOL)animated{
    [super viewWillAppear:animated];
    self.navigationItem.title=@"Job Details";
    [self setFontAndColrForView];
}
-(void)viewDidAppear:(BOOL)animated{
    [super viewDidAppear:animated];
    self.view.backgroundColor=kBackgroundColor;
    self.backgroundScrollView.backgroundColor=kBackgroundColor;
    if (self.jobId!=0) {
        [self performSelector:@selector(startNetworkActivity:) withObject:nil afterDelay:0.003];
        [self getJobDetail:self.jobId];
    }else{
        [self showJobDetail];
    }
    [self.backgroundScrollView setHidden:NO];

}

-(void)viewDidLayoutSubviews
{
    [super viewDidLayoutSubviews];
    
    //btn_bookJob.translatesAutoresizingMaskIntoConstraints=NO;
    if ([[dict_jobDetail safeObjectForKey:kJobSpecialNeed] isEqualToString:@""]) {
        [txt_SpecialNeeds setHidden:YES];
        [lbl_SpecialNeeds setHidden:YES];
        conForbtnYpos.constant=-(lbl_SpecialNeeds.frame.size.height+txt_SpecialNeeds.frame.size.height);
        
    }else{
        [txt_SpecialNeeds setHidden:NO];
        [lbl_SpecialNeeds setHidden:NO];
         conForbtnYpos.constant=5;
      
    }
   
   
}
/*
#pragma mark - Navigation

// In a storyboard-based application, you will often want to do a little preparation before navigation
- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender {
    // Get the new view controller using [segue destinationViewController].
    // Pass the selected object to the new view controller.
}
*/
#pragma mark-UserDefineMethods


-(void)getJobDetail:(int)jobID{
    SMCoreNetworkManager *networkManager;
    NSString *string_Url=[NSString stringWithFormat:@"%@",kJobDetail_API];
    networkManager= [[SMCoreNetworkManager alloc] initWithBaseURLString:string_Url];
    networkManager.delegate = self;
    NSMutableDictionary *dict_JobRequest=[[NSMutableDictionary alloc] init];
    [dict_JobRequest setSafeObject:self.sitterInfo.sitterId forKey:kUserId];
    [dict_JobRequest setSafeObject:self.sitterInfo.str_TokenData forKey:kToken];
    [dict_JobRequest setSafeObject:[NSNumber numberWithInt:self.jobId] forKey:kJobId];
    [dict_JobRequest setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
    [networkManager getJobDetail:dict_JobRequest forRequestNumber:2];
}
-(void)setFontAndColrForView{
    
    //Heading label
    [lbl_JobNumber setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_StartDate setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_EndDate setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_ChildrenCount setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_Address setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_Area setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_JobStatus setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_SpecialNeeds setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_specialInstruction setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_ChildDetail setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_Rate setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    [lbl_Total setFont:[UIFont fontWithName:Roboto_Medium size:FontSize12]];
    //Value label
    [lbl_ShowJobNumber setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_ShowStartDate setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_ShowEndDate setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_ShowChildCount setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_ShowAddress setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_ShowArea setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_ShowJobStatus setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_ShowRate setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [lbl_ShowTotal setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [txt_SpecialNeeds setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    [txt_specialInstruction setFont:[UIFont fontWithName:Roboto_Regular size:FontSize12]];
    
    [lbl_JobNumber setTextColor:kColorGrayDark];
    [lbl_StartDate setTextColor:kColorGrayDark];
    [lbl_EndDate setTextColor:kColorGrayDark];
    [lbl_ChildrenCount setTextColor:kColorGrayDark];
    [lbl_Address setTextColor:kColorGrayDark];
    [lbl_Area setTextColor:kColorGrayDark];
    [lbl_JobStatus setTextColor:kColorGrayDark];
    [lbl_Rate setTextColor:kColorGrayDark];
    [lbl_Total setTextColor:kColorGrayDark];
    //[lbl_SpecialNeeds setTextColor:kColorGrayDark];
    [lbl_ShowJobNumber setTextColor:kColorGrayDark];
    [lbl_ShowStartDate setTextColor:kColorGrayDark];
    [lbl_ShowEndDate setTextColor:kColorGrayDark];
    [lbl_ShowRate setTextColor:kColorGrayDark];
    [lbl_ShowTotal setTextColor:kColorGrayDark];
    [lbl_ShowSpecialNeeds setTextColor:kColorGrayDark];
    [lbl_ShowChildCount setTextColor:kColorGrayDark];
    [lbl_ShowAddress setTextColor:kColorGrayDark];
    [lbl_ShowJobStatus setTextColor:kColorGrayDark];
    [lbl_ShowArea setTextColor:kColorGrayDark];
    [lbl_City setTextColor:kColorGrayDark];
    [lbl_ShowCity setTextColor:kColorGrayDark];
    [lbl_State setTextColor:kColorGrayDark];
    [lbl_ShowState setTextColor:kColorGrayDark];
    [lbl_ZipCode setTextColor:kColorGrayDark];
    [lbl_ShowZipCode setTextColor:kColorGrayDark];
    txt_SpecialNeeds.textColor=kColorGrayDark;
    txt_specialInstruction.textColor=kColorGrayDark;
    [scroll_View setBackgroundColor:kColorWhite];
    [txt_SpecialNeeds setContentInset:UIEdgeInsetsMake(-8, 0, 0, 0)];
    [txt_specialInstruction setContentInset:UIEdgeInsetsMake(-8, 0, 0, 0)];
    
}
-(void)showJobDetail
{
    lbl_ShowJobNumber.text=[dict_jobDetail safeObjectForKey:kJobId];
    lbl_ShowStartDate.text=[dict_jobDetail safeObjectForKey:kJobStartDate];
    lbl_ShowEndDate.text=[dict_jobDetail safeObjectForKey:kJobEndDate];
    lbl_ShowChildCount.text=[dict_jobDetail safeObjectForKey:kActual_child_count];
    lbl_ShowRate.text=[@"$" stringByAppendingString:[dict_jobDetail safeObjectForKey:kRate]];
  //  int rate=[[dict_jobDetail safeObjectForKey:kRate] intValue]*[[dict_jobDetail safeObjectForKey:kChildCount] intValue];
    lbl_ShowTotal.text=[@"$" stringByAppendingString:[NSString stringWithFormat:@"%@", [dict_jobDetail safeObjectForKey:@"total_paid"]]];
   
    if ([[dict_jobDetail safeObjectForKey:kJobSpecialNeed] isEqualToString:@""]) {
        [txt_SpecialNeeds setHidden:YES];
        [lbl_SpecialNeeds setHidden:YES];
        if (![[dict_jobDetail safeObjectForKey:kNote_aboutJob] isEqualToString:@""]){
            CGRect splInsFrm=lbl_SpecialNeeds.frame;
            splInsFrm.origin.y=splInsFrm.origin.y;
            VerticallyAlignedLabel *lblSplIns=[[VerticallyAlignedLabel alloc]initWithFrame:splInsFrm];
            [lblSplIns setNumberOfLines:1];
            [lblSplIns setTextColor:lbl_SpecialNeeds.textColor];
            [lblSplIns setFont:lbl_SpecialNeeds.font];
            [lblSplIns setVerticalAlignment:VerticalAlignmentTop];
            [lblSplIns setText:lbl_specialInstruction.text];
            [lblSplIns sizeToFit];
            [self.backgroundScrollView addSubview:lblSplIns];
            
            CGRect splInsValueFrm=lblSplIns.frame;
            splInsValueFrm.origin.y=splInsValueFrm.origin.y+splInsValueFrm.size.height;
            VerticallyAlignedLabel *lblSplInsValue=[[VerticallyAlignedLabel alloc]initWithFrame:splInsValueFrm];
            [lblSplInsValue setNumberOfLines:10];
            [lblSplInsValue setTextColor:lbl_ShowJobNumber.textColor];
            [lblSplInsValue setFont:lbl_ShowJobNumber.font];
            [lblSplInsValue setVerticalAlignment:VerticalAlignmentTop];
            [lblSplInsValue setText:[dict_jobDetail safeObjectForKey:kNote_aboutJob]];
            
            [lblSplInsValue sizeToFit];
            [self.backgroundScrollView addSubview:lblSplInsValue];
            scrollContentSize=lblSplInsValue.frame.origin.y+lblSplInsValue.frame.size.height;
//            consForSpecialistruction.constant=-110;
//            [lbl_specialInstruction setHidden:NO];
//            [txt_specialInstruction setHidden:NO];
//            txt_specialInstruction.text=[dict_jobDetail safeObjectForKey:kNote_aboutJob];
        }else{
            [lbl_specialInstruction setHidden:YES];
            [txt_specialInstruction setHidden:YES];
        }
    }else{
        [txt_SpecialNeeds setHidden:NO];
        [lbl_SpecialNeeds setHidden:NO];
        CGRect splNeedFrm=lbl_SpecialNeeds.frame;
        splNeedFrm.origin.y=splNeedFrm.origin.y+splNeedFrm.size.height;
        VerticallyAlignedLabel *lblSplNeedValue=[[VerticallyAlignedLabel alloc]initWithFrame:splNeedFrm];
        [lblSplNeedValue setNumberOfLines:10];
        [lblSplNeedValue setTextColor:lbl_ShowJobNumber.textColor];
        [lblSplNeedValue setFont:lbl_ShowJobNumber.font];
        [lblSplNeedValue setVerticalAlignment:VerticalAlignmentTop];
        [lblSplNeedValue setText:[dict_jobDetail safeObjectForKey:kJobSpecialNeed]];
        [lblSplNeedValue sizeToFit];
        [self.backgroundScrollView addSubview:lblSplNeedValue];
        scrollContentSize=lblSplNeedValue.frame.origin.y+lblSplNeedValue.frame.size.height;
//        txt_SpecialNeeds.text=[dict_jobDetail safeObjectForKey:kJobSpecialNeed];
        if (![[dict_jobDetail safeObjectForKey:kNote_aboutJob] isEqualToString:@""]){
            [lbl_specialInstruction setHidden:NO];
            [txt_specialInstruction setHidden:NO];
            //txt_specialInstruction.text=[dict_jobDetail safeObjectForKey:kNote_aboutJob];
            CGRect splInsFrm=lblSplNeedValue.frame;
            splInsFrm.origin.y=splInsFrm.origin.y+splInsFrm.size.height;
            VerticallyAlignedLabel *lblSplIns=[[VerticallyAlignedLabel alloc]initWithFrame:splInsFrm];
            [lblSplIns setNumberOfLines:1];
            [lblSplIns setTextColor:lbl_SpecialNeeds.textColor];
            [lblSplIns setFont:lbl_SpecialNeeds.font];
            [lblSplIns setVerticalAlignment:VerticalAlignmentTop];
            [lblSplIns setText:lbl_specialInstruction.text];
            [lblSplIns sizeToFit];
            [self.backgroundScrollView addSubview:lblSplIns];
            
            CGRect splInsValueFrm=lblSplIns.frame;
            splInsValueFrm.origin.y=splInsValueFrm.origin.y+splInsValueFrm.size.height;
            VerticallyAlignedLabel *lblSplInsValue=[[VerticallyAlignedLabel alloc]initWithFrame:splInsValueFrm];
            [lblSplInsValue setNumberOfLines:10];
            [lblSplInsValue setTextColor:lbl_ShowJobNumber.textColor];
            [lblSplInsValue setFont:lbl_ShowJobNumber.font];
            [lblSplInsValue setVerticalAlignment:VerticalAlignmentTop];
            [lblSplInsValue setText:[dict_jobDetail safeObjectForKey:kNote_aboutJob]];
            [lblSplInsValue sizeToFit];
            [self.backgroundScrollView addSubview:lblSplInsValue];
            scrollContentSize=lblSplInsValue.frame.origin.y+lblSplInsValue.frame.size.height;
        }else{
            [lbl_specialInstruction setHidden:YES];
            [txt_specialInstruction setHidden:YES];
        }
    }
    [lbl_specialInstruction setHidden:YES];
    [txt_specialInstruction setHidden:YES];
    [txt_SpecialNeeds setHidden:YES];
    NSDictionary *dict_Address=[dict_jobDetail safeObjectForKey:kAddress];
    //DDLogInfo(@"Dict job =%@",dict_jobDetail);
    lbl_ShowJobStatus.text=[dict_jobDetail safeObjectForKey:kJobStatus];
    NSString *strAddress=[NSString stringWithFormat:@""];
    strAddress= [strAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[dict_Address safeObjectForKey:kStreetAddress]]];
   strAddress= [strAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[dict_Address safeObjectForKey:kCity]]];
    strAddress=[strAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[dict_Address safeObjectForKey:kState]]];
    strAddress=[strAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[dict_Address safeObjectForKey:kZipCode]]];
    if (![[dict_Address safeObjectForKey:kAddress1] isEqualToString:@""]) {
        strAddress=[strAddress stringByAppendingString:[NSString stringWithFormat:@"%@, ",[dict_Address safeObjectForKey:kAddress1]]];
    }
    if (![[dict_Address safeObjectForKey:kHotelName] isEqualToString:@""]) {
        strAddress=[strAddress stringByAppendingString:[NSString stringWithFormat:@"%@",[dict_Address safeObjectForKey:kHotelName]]];
    }
    //strAddress=[strAddress stringByAppendingString:[NSString stringWithFormat:@" %@",strAddress]];
    //strAddress=[strAddress stringByAppendingString:@" Testing strr old palasiya indore madhya pradesh"];
    lbl_ShowAddress.text=strAddress;
    [lbl_ShowAddress setVerticalAlignment:VerticalAlignmentTop];
    [lbl_ShowAddress setNumberOfLines:3];
    [lbl_ShowAddress setLineBreakMode:NSLineBreakByTruncatingTail];
    //[lbl_ShowAddress sizeToFit];
   [lbl_ShowAddress needsUpdateConstraints];
    lbl_ShowArea.text=[NSString stringWithFormat:@"%@",[dict_Address safeObjectForKey:kCity]];
    arrChildData=[dict_jobDetail objectForKey:kChildren];
    if (arrChildData.count<=1) {
        [btn_next setHidden:YES];
    }else{
        [btn_next setHidden:NO];
    }
    [btn_previous setHidden:YES];
    int viewContent=0;
    [scroll_View setPagingEnabled:YES];
    for (int i=0;i<arrChildData.count;i++) {
        NSDictionary *d=[arrChildData objectAtIndex:i];
        UIView *v=[[ApplicationManager getInstance] createViewForchildInfo:d frame:CGRectMake(viewContent+(scroll_View.frame.size.width*i), 0, scroll_View.frame.size.width, scroll_View.frame.size.height)];
        [scroll_View addSubview:v];
       
        totalCount=i;
    }
    [scroll_View setContentSize:CGSizeMake((scroll_View.frame.size.width*arrChildData.count)+viewContent, scroll_View.frame.size.height)];
    if ([[[dict_jobDetail safeObjectForKey:kJobStatus] lowercaseString] isEqualToString:@"cancelled"]) {
        [btn_bookJob setHidden:YES];
    }else{
        [btn_bookJob setHidden:NO];
    }
     [self.backgroundScrollView setContentSize:CGSizeMake(self.view.bounds.size.width,MAX(scrollContentSize,self.backgroundScrollView.frame.size.height))];
    [self.view layoutIfNeeded];
}

- (IBAction)onClick_BookJob:(id)sender {
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:kConfirmationMsgForBookJob delegate:self cancelButtonTitle:@"Yes" otherButtonTitles:@"No", nil];
    [alert setTag:100];
    [alert show];
    
}
- (IBAction)onClickNext:(id)sender {
    if (pageNo < totalCount) {
        btn_next.hidden = false;
        btn_previous.hidden = true;
        pageNo++;
        [scroll_View setContentOffset:CGPointMake(scroll_View.frame.size.width*pageNo, scroll_View.contentOffset.y) animated:YES];
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
        [scroll_View setContentOffset:CGPointMake(scroll_View.frame.size.width*pageNo, scroll_View.contentOffset.y) animated:YES];
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
        if (arrChildData.count == 1) {
            btn_next.hidden = true;
            btn_previous.hidden = true;
        }
    }
    
}
#pragma mark - AlertView delegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    if (alertView.tag==100 && buttonIndex==0) {
        [self startNetworkActivity:NO];
        SMCoreNetworkManager *networkManager;
        NSString *string_Url=[NSString stringWithFormat:@"%@",kAcceptJob_API];
        networkManager= [[SMCoreNetworkManager alloc] initWithBaseURLString:string_Url];
        networkManager.delegate = self;
        NSMutableDictionary *dict_JobRequest=[[NSMutableDictionary alloc] init];
        [dict_JobRequest setSafeObject:self.sitterInfo.sitterId forKey:kUserId];
        [dict_JobRequest setSafeObject:self.sitterInfo.str_TokenData forKey:kToken];
        [dict_JobRequest setSafeObject:kAPI_KeyValue forKey:kAPI_Key];
        [dict_JobRequest setSafeObject:[dict_jobDetail safeObjectForKey:kJobId] forKey:kJobId];
        [networkManager acceptJob:dict_JobRequest forRequestNumber:1];
    }else if (alertView.tag==101 && buttonIndex==0){
        [self.navigationController popToRootViewControllerAnimated:YES];
    }
    [self appDidLogout:alertView clickedButtonAtIndex:buttonIndex];

}
#pragma mark - SMCoreNetworkManagerDelegate
-(void)requestDidSucceedWithResponseObject:(id)responseObject
                                  withTask:(NSURLSessionDataTask *)task
                             withRequestId:(NSUInteger)requestId{
    NSDictionary *dict_responseObj=responseObject;
    [self stopNetworkActivity];
    switch (requestId) {
        case 1:
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
               
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:[dict_responseObj valueForKey:kMessage] delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
                [alert setTag:101];
                [alert show];
               
               
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            break;
        case 2://Get job detail
            if([[dict_responseObj valueForKey:kStatus] isEqualToString:kStatusSuccess]){
               
                DDLogInfo(@"job detail %@",dict_responseObj);
                dict_jobDetail =[[dict_responseObj safeObjectForKey:kData]safeObjectForKey:@"jobDetails"];
                [self showJobDetail];
                [self.view setNeedsDisplay];
            }
            else
            {
                [[ApplicationManager getInstance] showAlertForVC:nil withTitle:@"" andMessage:[dict_responseObj valueForKey:kErrorDisplayMessage]];
            }
            break;
            
            
        default:
            break;
    }
}
- (void)requestDidFailWithErrorObject:(id)error withRequestId:(NSUInteger)requestId{
    
    [self stopNetworkActivity];
    [[ApplicationManager getInstance] showAlertForVC:nil withTitle:nil andMessage:[error localizedDescription] ];
}
@end
