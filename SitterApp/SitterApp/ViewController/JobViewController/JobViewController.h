//
//  JobViewController.h
//  SitterApp
//
//  Created by Shilpa Gade on 03/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "JobListTableViewCell.h"
#import "ActiveJobDetailViewController.h"
@interface JobViewController : AppBaseViewController
{
    __weak IBOutlet UITableView *tbl_JobList;
    __weak IBOutlet UILabel *lbl_totalEarned;
    __weak IBOutlet UILabel *lbl_totalOwed;
    NSMutableDictionary *dict_JobRequest;

}
@property (weak, nonatomic) IBOutlet UIView *view_totalEarned;
@property(assign)int flag;
@property(nonatomic,weak)Sitter *sitterInfo;
@property(nonatomic,weak)JobList *jobList;
@property(nonatomic,strong)NSMutableArray *array_JobList;
@property(nonatomic,assign)int totalPages;
@property(nonatomic,assign)int currentPage;
@property(assign)BOOL isloading;


@end
