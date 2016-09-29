//
//  JobsViewController.m
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 16/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "JobsViewController.h"
#import "JobListViewController.h"
#import "JobDetailViewController.h"
#import "HomeViewTableViewCell.h"

@interface JobsViewController ()

@end
@implementation JobsViewController
- (void)viewDidLoad {
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    // tbl_jobs.layer.borderWidth = 1.0;
    //tbl_jobs.layer.borderColor = [UIColor blackColor].CGColor;
    dict_jobsData = [[NSMutableDictionary alloc]init];
     array_JobInfo =  [NSArray arrayWithObjects:@"Open Jobs Requests",@"Scheduled Jobs",@"Active Jobs",@"Completed Jobs",@"Cancelled Jobs",nil];
    self.navigationItem.title = @"Jobs";
    self.view.backgroundColor=kViewBackGroundColor;

}

- (void)didReceiveMemoryWarning {
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

// tableView DataSource delegate methods.
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section{
    return array_JobInfo.count;
}
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath{
    static NSString *CellIdentifier =@"TableViewController";
    HomeViewTableViewCell *cell = (HomeViewTableViewCell *)[tbl_jobs dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        NSArray *nib = [[NSBundle mainBundle] loadNibNamed:@"HomeViewTableViewCell" owner:self options:nil];
        cell = [nib objectAtIndex:0];
    }
    cell.selectionStyle = UITableViewCellSelectionStyleNone;
    cell.tintColor = [UIColor blackColor];
    cell.backgroundColor = kBackgroundColor;
    cell.lbl_ParentButton.text =[array_JobInfo objectAtIndex:indexPath.row];
   
    return cell;
}


-(void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath{
    [tableView deselectRowAtIndexPath:indexPath animated:YES];
    
    if (indexPath.row == 0) {
         
        [dict_jobsData setSafeObject:@"pending" forKey:kJobStatus];
    }
    else if (indexPath.row ==1)
    {
        [dict_jobsData setSafeObject:@"confirmed" forKey:kJobStatus];
    }else if (indexPath.row ==2)
    {
        [dict_jobsData setSafeObject:@"active" forKey:kJobStatus];
    }else if (indexPath.row ==3)
    {
        [dict_jobsData setSafeObject:@"completed" forKey:kJobStatus];
    }
    else
        [dict_jobsData setSafeObject:@"cancelled" forKey:kJobStatus];
    
    JobListViewController *jobList = [[JobListViewController alloc]initWithNibName:@"JobListViewController" bundle:nil];
    jobList.dict_dobDetail = [dict_jobsData mutableCopy];
    [self.navigationController pushViewController:jobList animated:YES];


}
- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section{
    return 0.01;
}

- (CGFloat)tableView:(UITableView *)tableView heightForFooterInSection:(NSInteger)section{
    return 0.05;
}
-(void)saveAction:(UIBarButtonItem *)sender{
    
    [self.navigationController popViewControllerAnimated:YES];
    
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
@end
