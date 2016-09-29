//
//  JobListViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 16/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"
#import "Parent.h"
#import "JobList.h"

@interface JobListViewController : AppBaseViewController
{
    __weak IBOutlet UITableView *tbl_jobList;
    NSMutableArray *array_jobList;
    JobList *jobInfo;
    int currentPage,totalPages;
    SMCoreNetworkManager *networkManager;
    NSMutableArray *array_jobsDetail;
    
}
@property (strong,nonatomic) NSMutableDictionary *dict_dobDetail;
@property (nonatomic) int jobType;
@property (nonatomic,assign) Parent *parentInfo;
@end
