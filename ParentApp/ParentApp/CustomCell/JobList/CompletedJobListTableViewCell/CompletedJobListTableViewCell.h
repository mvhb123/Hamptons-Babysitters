//
//  JobListTableViewCell.h
//  ParentApp
//
//  Created by Sandeep Kumar Singh on 16/04/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface  CompletedJobListTableViewCell: UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lbl_jobNumber;
@property (weak, nonatomic) IBOutlet UILabel *lbl_startDate;
@property (weak, nonatomic) IBOutlet UILabel *lbl_endDate;
@property (weak, nonatomic) IBOutlet UILabel *lbl_status;
@property (weak, nonatomic) IBOutlet UIButton *btn_cancle;
@property (weak, nonatomic) IBOutlet UILabel *lbl_babySitterName;
@property (weak, nonatomic) IBOutlet UILabel *lbl_viewJobNumber;
@property (weak, nonatomic) IBOutlet UILabel *lbl_viewStartDate;
@property (weak, nonatomic) IBOutlet UILabel *lbl_viewEndDate;
@property (weak, nonatomic) IBOutlet UILabel *lbl_viewStatus;
@property (weak, nonatomic) IBOutlet UILabel *lbl_rate;
@property (weak, nonatomic) IBOutlet UILabel *lbl_rateValue;
@property (weak, nonatomic) IBOutlet UILabel *lbl_totalCharge;
@property (weak, nonatomic) IBOutlet UILabel *lbl_totalChargeValue;
@property (weak, nonatomic) IBOutlet UIView *viewCellBG;


@property (nonatomic)int jobType;


@end
