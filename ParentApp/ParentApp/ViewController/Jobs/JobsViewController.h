//
//  JobsViewController.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 16/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import "AppBaseViewController.h"

@interface JobsViewController : AppBaseViewController
{
    __weak IBOutlet UITableView *tbl_jobs;
    NSArray *array_JobInfo;
    NSMutableDictionary *dict_jobsData;
    
}

@end
